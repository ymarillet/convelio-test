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
