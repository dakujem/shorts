# 🩳 shorts

![PHP from Packagist](https://img.shields.io/packagist/php-v/dakujem/shorts)
[![Build Status](https://travis-ci.org/dakujem/shorts.svg?branch=master)](https://travis-ci.org/dakujem/shorts)
![Nature Friendly](https://img.shields.io/badge/nature%20%F0%9F%8C%B3-friendly%20%F0%9F%92%9A-green)


Use this tool to shorten or limit personal names to desired length, or to use initials instead of a full name.\
Supports _Unicode_ / UTF-8 names.

- limit / cap name length (with the aim to keep the name as legible as possible):
    ```
    | John Roland Reuel Tolkien |
    | --> John R. Reuel Tolkien |   # last name priority
    | -----> John R. R. Tolkien |
    | -------> J. R. R. Tolkien |
    | ---------> J.R.R. Tolkien |
    | -----------> John Tolkien |
    | ---------------> J.R.R.T. |
    | -------------------> JRRT |
    | ---------------------> JT |
    |                           | 
    | John Roland Reuel Tolkien |
    | John R. R. Tolkien <----- |   # first name priority
    | John R. R. T. <---------- |
    | John R.R.T. <------------ |
    | J.R.R.T. <--------------- |
    | JRRT <------------------- |
    | JT <--------------------- |
    ```
- shorten a name using initials for part of the name (no length constraint):
    ```
    John Roland Reuel Tolkien:
  
    - J. R. R. Tolkien
    - John R. R. T.
    ```
- create initials (configurable separator):
  ```
  - JRRT
  - J. R. R. T.
  ```

> well i could not google out anything reasonable in well over 60 minutes so i decided to code it in less than that. how silly i was...


## TODO / in progress

The features and the public interface are in place.
The code is still ugly, don't fret. 🙊

I'm also deciding how to make it possible
for a custom name parser to be provided on the fly/by configuration,
so that special cases (like compound names) can easily be supported.
Bear with me. 🐻


**Supported**:
- unicode names
- arbitrary length


What is **not (yet) supported**:
- compound surnames (sorry folks, this may come later)
- non-word characters will be lost
    - `Bull, John`
- academic and other titles
    - `Bc. Foo Bar, Dr.Sc.`
    - `John Bull, Sr.`
- other writing systems than latin (may the will work, i'm just not testing them)

You will need to handle these yourself before/after passing them through the shortener.

**Possible future stuff**
- include a name parser to split the names
    - https://github.com/joshfraser/PHP-Name-Parser
    - https://github.com/theiconic/name-parser
    - in fact i intend to provide a possibility to use your own explode/implode functions so that the tool is as flexible as possible
- cache (since all of this starts to look a bit complex)
    - `Shorts::i()->reducer( ... )->cache()` / `Shorts::cache(Shorts::i()->reducer( ... ))`


## Usage

Limit (cap) names to desired lengths:
```php
Shorts::cap('Pablo Escobar', 10); // "P. Escobar"
Shorts::cap('Pablo Escobar', 2); // "PE"

Shorts::cap('John Ronald Reuel Tolkien', 20); // "John R. R. Tolkien"
Shorts::cap('John Ronald Reuel Tolkien', 16); // "J. R. R. Tolkien"
Shorts::cap('John Ronald Reuel Tolkien', 15); // "J.R.R. Tolkien"
Shorts::cap('John Ronald Reuel Tolkien', 8);  // "J.R.R.T."
Shorts::cap('John Ronald Reuel Tolkien', 4);  // "JRRT"
```
The above will try to keep the **last name legible**, unless the limit is too strict.\
Inverse version that will try to keep the **first name legible** is also available:
```php
Shorts::cap('Pablo Escobar', 10, Shorts::FIRST_NAME); // "Pablo E."
```

Shrink names using initials except for the last name:
```php
Shorts::shrink('John Ronald Reuel Tolkien'); // "J. R. R. Tolkien"
Shorts::shrink('Hugo Ventil');               // "H. Ventil"
```

Shrink names using initials except for the first name:
```php
Shorts::shrink('John Ronald Reuel Tolkien', Shorts::FIRST_NAME); // "John R. R. T."
Shorts::shrink('Hugo Ventil', Shorts::FIRST_NAME);               // "Hugo V."
```

Create "short" initials:
```php
Shorts::initials('John Ronald Reuel Tolkien'); // "JRRT"
Shorts::initials('Hugo Ventil');               // "HV"
```
... or "longer" version:
```php
Shorts::initials('John Ronald Reuel Tolkien', '.', ' '); // "J. R. R. T."
Shorts::initials('Hugo Ventil', '.', ' ');               // "H. V."
```

Each of the static methods has a non-static counterpart:
```php
Shorts::i()->limit( ... );      // Shorts::cap( ... )
Shorts::i()->reduce( ... );     // Shorts::shrink( ... )
Shorts::i()->toInitials( ... ); // Shorts::initials( ... )
```

Shorts also provides the ability co create a preconfigured formatter callable for each of the methods:
```php
Shorts::i()->limiter( ... )
Shorts::i()->reducer( ... )
Shorts::i()->initialsFormatter( ... )
```
These can be used as follows:
```php
$fmt = Shorts::i()->limiter(20); // will limit any input to 20 chars
$fmt('Foo Bar'); // this is equivalent to  Shorts::cap('Foo Bar', 20)
```

> Note the formatters can come handy when defining filters for templating languages, like Twig or Latte.


## Testing

`$` `composer test`


## Contributions

... are always welcome. Many times it is useful to just point out a use case the author have not thought about or come across.

