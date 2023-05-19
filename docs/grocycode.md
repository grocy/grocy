Grocycode
==========

Grocycode is, in essence, a simple way to reference to arbitrary Grocy entities.
Each Grocycode includes a magic, an entitiy identifier, an id and an ordered set of extra data.
It is supported to be entered anywhere Grocy expects one to read a barcode, but can also reference
Grocy-internal properties like specific stock entries, or specific batteries.

Serialization
----

There are three mandatory parts in a Grocycode:

1. The magic `grcy`
2. An entity identifer matching the regular expression `[a-z]+` (that is, lowercase english alphabet without any fancy accents, minimum length 1 character).
3. An object identifer matching the regular expression `[0-9]+`

Optionally, any number of further data without format restrictions besides not containing any double colons [0] may be appended.

These parts are then linearly appended, seperated by a double colon `:`.

Entity Identifers
----

Currently, there are three different entity types defined:

- `p` for Products
- `b` for Batteries
- `c` for Chores

Example
----

In this example, we encode a *Product* with ID *13*, which results in `grcy:p:13` when serialized.

Product grocycodes
----

Product grocycodes extend the data format to include an optional stock id, thus may reference a specific stock entry directly.

Example: `grcy:p:13:60bf8b5244b04`

Battery grocycodes
----

Currently, Battery grocycodes do not define any extra fields.

Chore grocycodes
----

Currently, Chore grocycodes do not define any extra fields.

Visual Encoding
----

Grocy uses DataMatrix 2D (or alternatively Code128 1D) Barcodes to encode grocycodes into a visual representation. In principle, there is no problem with using
other encoding formats like QR codes; however DataMatrix uses less space for the same information and redundancy and is a bit
easier read by 2D barcode scanners, especially on non-flat surfaces.

You can pick up cheap-ish used scanners from ebay (about 45€ in germany). Make sure to set them to the correct keyboard emulation,
so that the double colons get entered correctly.


Notes
---
[0]: Obviously, it needs to be encoded into some usable visual representation and then read. So probably you only want to encode stuff that can be typed on a keyboard.
