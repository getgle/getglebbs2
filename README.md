federates with mastodon, pleroma, gnusocial and diaspora

# getglebbs2
GetgleBBS Sucessor with a cleaner codebase and MySQL instead of flatfiles.
--------------------------------------------------------------------------
The code is self documenting :^) i dont have to write any documentation.

Pros:

1. Runs much better than the original GetgleBBS,

2. Cleaner codebase than GetgleBBS,

3. Very modular and easy to work with object-oriented code.

Cons:

1. Bumping is not implmented yet

2. The SQL table is pretty weirdly structured because I had to make this compatable with the original getglebbs's data structures.

3. Possibly insecure as I used an older MySQL wrapper.  I plan on porting all the database stuff to PDO soon.
---
TRY IT ON http://getgle.org
