# tgif
UVA student evaluations browser

## how works? overview
1. run scripts to put data into databases
2. serve php + html files
3. browse html files to browse data

```
SCRIPTS:
populate_offerings.rb -- gets all class offerings from lou's list
populate_evals.rb     -- get all class evals from UVA

PHP SERVER STUFF:
classes.php -- returns info about all classes in JSON
class.php   -- returns info about a single class in JSON

HTML INTERFACE:
viewclasses.html -- view all classes
viewclass.html   -- view a single class
```

## future
- add features helpful for registration, e.g. 'offered this semester!' tag
- sort data by clicking lowest headers
- pre-generate data ????
- add courseforum review data?