# shorts

> well i could not google out anything reasonable in well over 60 minutes so i decided to code it in less than that. how silly i was...

---

I can already tell that it's not an easy task to do it right.

---

Usage:
```php
// shorten the name to 10 characters
$s = new Dakujem\Shorts; // or Dakujem\Shorts::i()
$s->cull('Pablo Escobar', 10); // "P. Escobar"
$s->cull('Pablo Escobar', 2); // "PE"
```


Supports:
- unicode names


---


## Testing

`$` `composer test`


## Possible future stuff

- include a name parser to split the names
    - https://github.com/joshfraser/PHP-Name-Parser
    - https://github.com/theiconic/name-parser
    