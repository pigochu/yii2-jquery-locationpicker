phpdevserver Change Log
========================

0.2.5 2018-03-31
 - Fixed bug : location option doesn't work in coordinate picker (issue #17)

0.2.4 2018-02-24
 - CoordinatesPicker fix for virtual attributes , thanks @deathburger

0.2.3 2018-01-20
----------------
 - Fixed bug  : Issue #13 , LocationPicker value lost on update

0.2.2 2016-12-03
----------------
 - Fixed bug : CoordinatesPicker attribute value lost
 - remove version 0.2.1
 - remove version 0.0.1~0.1.4

0.2.1 2016-12-02
---------------
  - Fixed bug : CoordinatesPicker can not set id

0.2.0 2015-12-28
----------------
 - CoordinatesPicker change
   - 'enableMapTypeControl' is deprecated , please use 'mapOptions'
   - if use 'enableMapTypeControl' , browser develop console will display warning message
   - 'mapOptions' can set all gmap's control options
   - add 'searchBoxPosition'

0.1.5 2015-12-21
-----------------
 - Fixed bug : CoordinatesPicker can not use 'this' object in "onchanged" and "oninitialized" event


0.1.4
-----------------
 - I don't know why has this version ....


0.1.3 2015-12-01
------------------
 - Fixed bug: CoordinatesPicker Undefined offset: 0 when coordinates is empty string


0.1.2 2015-11-17
------------------
 - Fixed bug : if never moved picker on CoordinatesPicker , the ActiveField will lost value when submit
 - Change searchbox default hidden , when map rendered then display

0.1.1 2015-11-14
------------------

 - Fixed #1 : "defined variable" can not use in Class property less than PHP 5.6

0.1.0 2015-11-12
------------------
 - Add CoordinatesPicker widget


0.0.2 2015-11-11
------------------
- Fix bug : LocationPickerWidget must assign google map api key 

0.0.1 2015-11-11
------------------

- First Test version

