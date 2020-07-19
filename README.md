# tgif
UVA student evaluations browser

## how works? overview
1. run scripts to put data into databases
2. use php files to browse data

```
populate_offerings.rb -- script; gets all class offerings from lou's list
populate_evals.rb     -- script; get all class evals from UVA

classes.php -- browser for all classes
class.php   -- browser for single class
queries.php -- helper functions for other php files
```

## future
- in general just make better (group professors, display prof. avgs., display more data, add css)
- prob go for a DB-serving php backend -> HTML that uses backend
	- vs. the current php mess
- pre-generate some of the data? (since all of it is static and could all basically be static files)
- add courseforum review data?