# past2
A Password complexity simplification system inspired by Android's Pattern system.

So, after I get my tech support business, I'll probably get to this soon.
My initial goal right now is to finish `prototype.php` which can be used
to generate a static HTML webpage that contains a single pattern cipher.
But in the future, I want to create a filetype that can be used to store
ciphers, and distribute the GUI apart from the cipher.

For the full version, I want to use GTK to create a native GUI that can be
distributed to any platform, and respect user theme choices. Particularly
because I like the consistency on my Pinephone, and I don't want to invalidate
that for my primary password manager, but it's also just a nice feature that
I'm sure others will appreciate too.

For the filetype, I want it to be charset independant. The magic header should
be `PAST2`, hex `50 41 53 54 32` and the charset should probably
be referenced by the file, along with the size of the cipher as a `uint8`.
I'm not too clear on how charsets are applied to
data so a charset ref may not even be necessary. A newline control character
should be used as both the ending of the header, and as a delimiter for
each row of characters in the cipher.
