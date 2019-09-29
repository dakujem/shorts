# 🩳 shorts
> well i could not google out anything reasonable in well over 60 minutes so i decided to code it in less than that. how silly i was...

Use this to shorten/limit personal names to desired length, or to use initials instead of the full name.


## TODO / in progress

The features are in place, i'm currently deciding on what the public interface should look like. The code is ugly, don't fret.

I'm also deciding how to make it possible for a custom name parser to be provided on the fly/by configuration, so that special cases (like comound names) can easily be supported. Bear with me. 🐻


## Usage

Create an instance of 🩳...
```php
$s = new Dakujem\Shorts; // or Dakujem\Shorts::i()
```

Limit names to desired lengths:
```php
$s->reduceFirst('Pablo Escobar', 10); // "P. Escobar"
$s->reduceFirst('Pablo Escobar', 2); // "PE"

$s->reduceFirst('John Ronald Reuel Tolkien', 20); // "John R. R. Tolkien"
$s->reduceFirst('John Ronald Reuel Tolkien', 16); // "J. R. R. Tolkien"
$s->reduceFirst('John Ronald Reuel Tolkien', 15); // "J.R.R. Tolkien"
$s->reduceFirst('John Ronald Reuel Tolkien', 8);  // "J.R.R.T."
$s->reduceFirst('John Ronald Reuel Tolkien', 4);  // "JRRT"
```
The above will try to keep the **last name legible**, unless the limit is too strict.\
Inverse version that will try to keep the **first name legible** is also available:
```php
$s->reduceLast('Pablo Escobar', 10); // "Pablo E."
```

Use initials except for the last name:
```php
$s->keepLast('John Ronald Reuel Tolkien'); // "J. R. R. Tolkien"
$s->keepLast('Hugo Ventil');               // "H. Ventil"
```

Use initials except for the first name:
```php
$s->keepLast('John Ronald Reuel Tolkien'); // "John R. R. T."
$s->keepLast('Hugo Ventil');               // "Hugo V."
```

Use "short" initials:
```php
$s->initials('John Ronald Reuel Tolkien'); // "JRRT"
$s->keepLast('Hugo Ventil');               // "HV"
```
... or "longer" version:
```php
$s->initials('John Ronald Reuel Tolkien', '.', ' '); // "J. R. R. T."
$s->keepLast('Hugo Ventil', '.', ' ');               // "H. V."
```


Supports:
- unicode names


What is not supported:
- compound surnames (sorry folks, this may come later)
- academic and other titles
    - Bc. Foo Bar, Dr.Sc.
    - John Bull, Sr.
    - you need to parse these yourself
- other writing systems than latin (may the will work, i'm just not testing them)

---


## Testing

`$` `composer test`


## Possible future stuff

- include a name parser to split the names
    - https://github.com/joshfraser/PHP-Name-Parser
    - https://github.com/theiconic/name-parser
    
