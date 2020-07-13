Yohann Marillet's global explanations on refactoring
====================================================

Easy install / test
-------------------
I created a docker env + a self documented Makefile base in order to make life easier to run the project in a dev environment.

This Makefile uses a little "hack" in order to use arguments on make targets.

Makefile is a deliberate choice here, main reason to use it over another system alike is because it's more portable.

Good practices + Deprecations
-----------------------------
1. Singleton should never be used and should be replaced by an implementation of the dependency injection principle. I consider Singleton as an antipattern.
   Since it'll take a certain amount of time to do that, I'll just mention it for now but it should be done at some point.
2. public properties should be avoided when possible, apart from pure Data Transfer Objects (objects containing only properties + getters/setters)
3. variables and properties should always have typehints, and when possible have typechecks. It's preferable for a defined variable not to have multiple types when possible to make the code simplier to understand. If not using most recent PHP versions to force data type on definition, we can use an external library to make assertions that will throw exceptions upon unexpected type. 
4. we'll use PSR's code formatting. In order to ensure that, we should plug some library to a CI and/or on git hooks to ensure the file formats.

Final notes
-----------
My aim was to get the code cleaner while not breaking the app (= the existing tests) as they are the only source of truth when you don't know a codebase.

Since we should have kept ~1 hour time of refactoring, and it's already been almost 2, I'll stop there for now but we could do much more to clean up all this mess ;)

* next step should be to find and remove dead code (for example, the summary is never actually replaced in the example nor the tests, etc.)
* now it's cleaner, we should get more code coverage through unit or functional tests. We should priorize high value tests, such as on the new replacers.
* there are some optimizations that can still be made in the replacers, e.g. lazy loading some data.
* get rid of the singleton trait, add a dependency injection container
* then we can think about upgrading to a proper version of PHP so we can use its feature such as typehinting to clarify the code as much as possible
