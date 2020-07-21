#!/usr/bin/env ruby

# script that fetches class offerings
# from lou's list via CSV and
# and puts them into a SQLite database

require 'net/http'
require 'csv'
require 'sqlite3'

# which semesters' data to collect
semesters = [
#	1208, 1206, 1202, 1201,
	1198, 1196, 1192, 1191,
	1188, 1186, 1182, 1181,
	1178, 1176, 1172, 1171,
	1168, 1166, 1162, 1161,
	1158, 1156, 1152, 1151,
	1148, 1146, 1142, 1141,
	1138, 1136, 1132, 1131,
	1128, 1126, 1122, 1121,
	1118, 1116, 1112, 1111,
	1108, 1106, 1102, 1101,
	1098, 1096, 1092,
	1088, 1086, 1082,
	1078, 1076, 1072,
	1068
]

# open and set up SQLite
db = SQLite3::Database.open './data/data.db'
db.execute(%q{
	create table if not exists offerings (
		sem INTEGER,
		cln INTEGER,
		grp TEXT,
		num TEXT,
		dsc TEXT,

		unique(sem, cln)
	)
})

DELIVERY_URI = URI 'https://louslist.org/deliverData.php'
def get_csv(sem)
	options = { Group: 'CS', Semester: sem }
	content = Net::HTTP.post_form(DELIVERY_URI, options).body

	CSV.parse content.force_encoding('utf-8').strip, headers: true
end

semesters.reverse.each do |sem|
	STDERR.print "processing semester #{sem}"

	get_csv(sem).each do |row|
		cln = row['ClassNumber'].to_i
		grp = row['Mnemonic']
		nbr = row['Number'] # theres a few classes that have non-numeric nbrs
		dsc = row['Title']

		next if cln == 0 # there's ~one class that has invalid cln

		db.execute "insert into offerings values (?, ?, ?, ?, ?)", [sem, cln, grp, nbr, dsc]
		STDERR.print '.'
	end

	STDERR.print " OK!\n"
end

STDERR.puts "done"