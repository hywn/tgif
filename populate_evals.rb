#!/usr/bin/env ruby

# script that fetches class evals
# from some UVA course eval site
# and puts them into a SQLite database

# note: needs a _shibsession cookie via ARGV[0]

require 'net/http'
require 'sqlite3'

cookie = ARGV[0]

abort 'no cookie' if not cookie

# set up scraping rig
EVALSURI = URI 'https://evals.itc.virginia.edu/course-selectionguide/pages/SGMain.jsp'
HEADERS  = { Cookie: cookie }
CLIENT   = Net::HTTP.new EVALSURI.host, EVALSURI.port
CLIENT.use_ssl = true
def query(params={})
	EVALSURI.query = URI.encode_www_form params

	CLIENT.get(EVALSURI, headers=HEADERS).body
end
def get_evals(grp, num)
	contents  = query({cmp: "#{grp},#{num}"})
	offerings = contents.scan /<td colspan="2">.+?(?=<td colspan="2">|<\/html>)/m

	offerings.filter_map do |text|

		responded, total = text.match(/\[(\d+) of (\d+) responded\]/).captures.map {|x| x.to_i}

		professors = text.scan /<td><span.+?<\/table>/m

		prof_ratings = professors.filter_map do |text|

			*ysc, prof_id    = text.match(/(\d+)_(\d+)_(\w+):(\w+)/).captures
			hour_percentages = text.scan(/(?<=<span class="percentage">)\d+\.\d+/)        .map {|x| x.to_f}
			means            = text.scan(/(?<=<span class="mean">)\d+\.\d+/)              .map {|x| x.to_f}
			# sometimes std will be NA because only one person took the class (see AIRS 410)
			stds             = text.scan(/(?<=<span class="statsubheader2"> \()(?:\d+\.\d+|NA)/) .map {|x| x.to_f}

			#
			# sometimes evals will only have 2 means/stds for no apparent reason ??? see BIOM 200, Yong Kim
			if ysc.length != 3
				abort "(fatal: encountered strange y_s_c: #{ysc})"
			end
			if means.length != 5
				STDERR.print "(encountered strange means: #{means})"
				means = [nil, nil, nil, nil, nil]
			end
			if stds.length != 5
				STDERR.print "(encountered strange stds: #{stds})"
				stds = [nil, nil, nil, nil, nil]
			end

			year, sem_no, cln = ysc.map {|x| x.to_i}

			# some older classes have non-5-number CLNs (e.g. MATH 114)...
			# since they're older, I think it's fine to skip them
			next if cln.to_s.length < 5

			sem = ((year - 1900).to_s + sem_no.to_s).to_i

			[sem, cln, prof_id, hour_percentages, means, stds]
		end

		next if prof_ratings.empty?

		prof_ratings.map {|rating| [responded, total, *rating]}
	end .flatten 1
end

# evals join offerings using (sem, cln) ??
db = SQLite3::Database.open 'data.db'
db.execute(%q{
	create table if not exists evals (
		sem INTEGER,
		cln INTEGER,
		prf TEXT,

		responded INTEGER,
		total     INTEGER,

		h1 REAL,
		h2 REAL,
		h3 REAL,
		h4 REAL,
		h5 REAL,

		m1 REAL,
		m2 REAL,
		m3 REAL,
		m4 REAL,
		m5 REAL,

		s1 REAL,
		s2 REAL,
		s3 REAL,
		s4 REAL,
		s5 REAL,

		unique(sem, cln, prf)
	)
})

classes = db.execute('select distinct grp, num from offerings')

classes.each do |grp, num|
	STDERR.print "processing #{grp} #{num}"

	get_evals(grp, num).each do |responded, total, sem, cln, prof_id, hours, means, stds|

		begin
			db.execute 'insert into evals values (?,?,?, ?,?,  ?,?,?,?,?,  ?,?,?,?,?,  ?,?,?,?,?)', sem,cln,prof_id, responded,total, *hours, *means, *stds
		rescue SQLite3::ConstraintException
			# UVA groups AAS 101 under AAS 1010, so it'll try to
			# break the unique constraint a few times
			STDERR.print '(constraint exception thrown)'
		end

		STDERR.print '.'

	end

	STDERR.puts ' OK!'
end

STDERR.puts "done"