**App Name : TUndeL Framework<br/>Current Version : 0.8<br/>Developer : Sina Kadkhodaei**<br/>

  A mini framework I call TUndeL (Two UnderLine) that based on MVC architecture and PHP technology

---

## Minimum requirements
Software         |Version
-----------------|------
php              |7.3.11
mysql (if use)   |8.0.18
mongodb (if use) |4.2.3

---

## To use it with built-in server
Run this terminal command in Public directory :
``` code
php -S localhost:80 __Run.php
```
and goto "http://localhost:80" url

## To use it on shared hosts
change "/.htaccess [Main]" to "/.htaccess"

---

## *Full features and documentation will be available soon*

# Features summary
* Routes :
  + Set extension for all routes
  + Set csrf for specified routes
  + Add regex pattern as rule for using in route parameters
  + Methods :
    - addRule
    - getRule
    - getRoute (RouteName, Parameters)
    - getNormalUrl
    - getRoutes [Return all routes]
    - get , post and any :
        - run [Run an action in a controller or a anonymous function]
        - csrf [Check csrf token or not?]
        - middleware (Middleware name , Run for all subroutes?)
        - middlewareExceptions [List parent middlewares that dont want run for this route]
        - name [Name of this route for get url by call it as it name]
* Middlewares :
  + run
  + next
  + fail
  + failBack
* Controllers :
  + Work with datalayer and views
* Views :
  + metaCsrf [Create meta tag with csrf token content ]
  + putCsrf [Create hidden input tag with csrf token value ]
  + loadSection [In a layout call this function to reserve a namespace in sections]
  + startSection [Start a block code for replacing to a load section namespace]
    - endSection [End a section block]
  + loadView [This function should call at end of view code]
  + perviousPage [Return url of pervious page]
* Data Layer :
  + Properties :
    - table [Table name]
    - primaryKey [Increment identity key name]
    - fieldTypes [Type of columns that be called in queries] :
        - TypeInt , TypeFloat , TypeBool , TypeString , TypeTimeStamp , TypeArray , and TypeFile
  + select , where , join , on , orderBy , groupBy , insert , into , delete , update , where and ...
* Session :
  + Session::indexName([can be a value])

<!-- * Functions :

  + Methods -->

#### Constants :
| Name          | Example value                     |
|---------------|-----------------------------------|
| PrjDir        | "C:\\MyServer\\TUndeL"            |
| CurrentRoute  | "/Foo/Bar"                        |
| CurrentUrl    | "http://localhost/TUndeL/Foo/Bar" |
| OriginUrl     | "http://localhost/TUndeL"         |
| RequestMethod | "get"                             |

-----

# Logs

> Version 0.3.3<br/>Release in Thursday December 12, 2019 22:44:06 

01. *Session encrypt has better*
02. *Disable session encrypt from .configs.json*

> Version 0.3.4<br/>Release in Friday December 13, 2019 00:38:45 

01. *Field casting in data layer*
02. *Hash like hash password with Tools\Hash::make and Hash::check(Hashed , Text)*

> Version 0.3.5<br/>Release in Friday December 13, 2019 10:11:07 

01. *Use events for do somthing you want [__System::boot(),__System::shutdown]*
02. *Hash like hash password with Tools\Hash::make and Hash::check(Hashed , Text)*
03. *Change app name to TUndeL*

> Version 0.3.5.3<br/>Release In Friday December 13, 2019 13:27:56 

01. *Fix bugs*

> Version 0.3.5.6<br/>Release In Friday December 13, 2019 14:20:14 

01. *Fix built-in server bugs*

> Version 0.4.5<br/>Release In Saturday January 11, 2020 16:42:01 

01. *Fix query builder select join*
02. *Add join to query builder CUD (No for read)*
03. *Use dot (.) for addressing in addresses like middlewares , views and ...*
04. *Use Response::redirectHead when you want to redirect faster*
05. *You can directly write code for run when route's start runing in Route::...->Run(func(){})*
06. *__Run.php moved to public*
07. *Use function e() for echo text with escape html and and use r() for echo raw*
08. *Use function bind for bind array with an array or string*
09. *Use function bindSql for bind array with an SQL query*
10. *Use function stop() for drop an exception for test and debuging*
11. *Session::__ALL() for get $_SESSION*
12. *Session::put and Session::delete*
13. *Use function valueSection(name,value) for give data to a section in view*
14. *Function loadPartial() for using __App\__Views\__Partials*
15. *Function perviousPage()*
16. *Use __Events\__System for control application booting and shuting down*
17. *Use __Events\__Errors for handling application errors*
18. *You can add function and variables to Tools\__RootFunctions and use it as globaly*
19. *Use Response::openFile() for open stream*
20. *Use Response::back() to redirect to pervious route*
21. *Use SpeedTest for test speed as micro time*
22. *Use Storage::... for work with __Protected folder*
23. *Validator::check()*

> Version 0.5.1<br/>Release In Friday January 24, 2020 21:57:29 

01. *Fix host version bugs*
02. *Add ".htaccess [main]" file for use in host*

> Version 0.8<br/>Release In Friday November 20, 2020 15:46:49 

01. *Minor bugs fixed*
02. *Routing gets better*
03. *Validator has message class*
04. *Query builder gets better*
05. *and good major changes*
