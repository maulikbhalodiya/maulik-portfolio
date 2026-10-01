# Contributing

Thank you for considering a contribution. This document is short on purpose. It lists
the gates a pull request must pass and the handful of rules that those gates cannot
automatically enforce.

---

## The gates

A pull request is not mergeable until every one of these is green. They run in
`.github/workflows/ci.yml`:

| Gate | Command | Requirement |
|---|---|---|
| `composer validate` | `composer validate --strict` | must pass |
| `composer audit` | `composer audit --locked` | no known advisories |
| WPCS version assertion | explicit check in CI | **3.4.1 or later** |
| PHP syntax | `php -l` on every non-vendor PHP file | no syntax errors |
| PHP_CodeSniffer | `vendor/bin/phpcs --standard=phpcs.xml.dist .` | **zero errors and zero warnings** |
| PHPStan | `composer stan` | no findings at level 5 |
| Stylelint | `npm run lint:css` | clean |
| ESLint | `npm run lint:js` | clean |
| Token drift | `npm run tokens && git diff --exit-code -- assets/styles/generated/` | no drift |
| No SCSS enqueued from PHP | grep assertion | no match |
| CSS build | `npm run build:css` | `style.css` exists |
| Content rules | em dash, en dash, `mailto`, raw email greps | no hits |
| WordPress standards | required file existence, forbidden legacy directory absence | pass |

**Warnings are failures.** phpcs must report zero of them. A PR that introduces a
warning does not pass, and fixing it by changing the ruleset is not a fix.

There is no PHPUnit step, and that is deliberate rather than an oversight. See the
`_comment` key in `composer.json` for the full reasoning. The theme is declarative:
templates are `.html` and carry no executable logic, patterns run on `init` with no
meaningful inputs, and there are no custom post types, taxonomies, shortcodes or form
handlers to test.

---

## WordPress Coding Standards are zero tolerance

`phpcs.xml.dist` enables `WordPress-Extra` (which already includes `WordPress-Core`),
`WordPress-Docs`, `Generic.Commenting.Todo` and `PHPCompatibilityWP` with
`testVersion 7.4-` and `minimum_wp_version 6.7`.

`WordPress-Docs` is on on purpose. Every class, method, property, function and constant
needs a docblock with `@param`, `@return` and `@var` where applicable. A missing
`@param` tag is a real finding, not a nit.

### What phpcbf can and cannot do

`composer lint:fix` fixes whitespace, indentation, brace and parenthesis placement, and
some naming and spacing sniffs.

It does **not** fix:

- escaping
- sanitizing
- missing nonces
- i18n problems
- unprepared SQL

It also does **not** fix Yoda conditions, because mechanically swapping operands can
change evaluation order and side effects. Yoda conditions in this theme are written and
reviewed by hand.

**Never accept a phpcbf run as evidence that the code is safe.** Read the diff.

---

## Reviewer rules

These apply to human reviewers and to the AI reviewer equally. They are the rules that
keep the gates meaningful.

1. **Never add a `phpcs:ignore` or `phpcs:disable` comment.** Suppressing the sniff does
   not fix the code. It hides the problem, and it teaches the next reader that the
   standard is optional. If you find yourself reaching for one, the answer is a code
   change.

2. **Never edit `phpcs.xml.dist` to weaken a check.** Removing a rule so a violation
   disappears is not a fix, it is deleting the alarm and leaving the fire. The one
   legitimate exception is a rule that is factually wrong for this codebase, and even
   then it belongs in its own pull request with a comment explaining why.

3. **Never weaken or delete a test.** Not a CI step, not an assertion, not a lint rule
   masquerading as a test. A pull request that only passes by removing a gate is not a
   passing pull request.

4. **Never propose code that itself fails the gates.** Any code in a review comment
   must pass `phpcs.xml.dist` with zero errors and zero warnings. If you cannot write it
   compliantly, describe the fix in prose and let the author write it.

5. **Report only what you can point at.** Every finding needs a file path and a line
   number. If you cannot produce both, you do not have a finding. If the diff is clean,
   say it is clean. That is a useful answer and a short one.

---

## The no-dashes rule

**No em dashes and no en dashes. Anywhere.**

Not in code. Not in comments. Not in user-facing copy. Not in documentation. Not in
commit messages. Not in pull request descriptions. Not in review comments. Not in your
own suggestions.

This includes the characters U+2014 (em dash) and U+2013 (en dash). CI greps the entire
repository for both and fails on any hit outside `node_modules/` and `vendor/`.

Use instead:

| Instead of | Write |
|---|---|
| an em dash joining clauses | a comma, or a full stop and a new sentence |
| an em dash for an aside | parentheses |
| an em dash before a list | a colon |
| an en dash in a range | the word `to`, as in `1 to 6` |
| an en dash for a parenthetical | a comma, or parentheses |

CI also fails on `mailto` links and on anything matching an email address pattern.
Contact is a form with an intent dropdown, not an address in the markup. Raw addresses
are harvested by automated crawlers within hours of a site going live.

---

## The architecture rules you must not break

These are in `README.md` in full and in `.audit-prompts/` in depth. The short version of
the ones that break silently:

- **Never create `block-templates/` or `block-template-parts/`.** Core checks
  `file_exists()` on the stylesheet directory and switches to legacy handling if either
  exists, which silently breaks `templates/`. Use `templates/` and `parts/`.
- **Template parts are `.html` and cannot contain PHP.** Anything needing i18n or a
  dynamic value goes in `patterns/*.php`.
- **Patterns run on `init`.** `is_single()`, `get_post()`, `the_title()` and the loop
  are unavailable there.
- **`render.php` must be side-effect free.** It is included once per block instance, so
  declaring a function or class inside it is a fatal error on the second instance. It
  must also return a string, never `echo`, so emitted whitespace never becomes visible
  output.
- **`block.json` `"type": "string"` is a type assertion, not sanitization.** Attribute
  values are untrusted at render time and must be escaped per context.
- **`style.css` is not auto-enqueued in a block theme.** It must be enqueued explicitly.
- **Every PHP file opens with `defined( 'ABSPATH' ) || exit;`** after its docblock. A
  namespaced file puts `namespace X;` first, because PHP requires it.
- **`should_load_separate_core_block_assets` and `should_load_block_assets_on_demand`
  must both be `true`.** Removing either is a blocking finding.
- **Never create `index.php`.** Block themes do not need it, and CI fails if it appears.

---

## Scope discipline

The initial scaffold deliberately excludes custom post types, taxonomies, shortcodes,
form handling and `register_block_type()` in PHP. Project data lives in block attributes
inside post content.

That is not an accident to be quietly fixed. If a contribution needs one of those,
raise it in an issue before writing the code, and expect the discussion about whether a
portfolio theme should introduce a database schema at all.

Similarly, there are no skill percentage bars, no progress meters and no star ratings.
A percentage with no data source is a claim the site cannot substantiate.

---

## Build and tooling

Use the Dart Sass CLI, not `wp-scripts build`, to compile SCSS. Stock `@wordpress/scripts`
has no `includePaths`, will not compile a standalone `style.scss`, and does not expose
`silenceDeprecations`.

**Write `@use` and `@forward` only. Never `@import`.** Deprecated in Dart Sass 1.80.0,
removed in 3.0.0.

If you change `theme.json`, run `npm run tokens` and commit
`assets/styles/generated/_theme-json-tokens.scss`. CI fails on drift.

If you add a palette entry, **append it. Never reorder.** Translation of theme.json names
uses the JSON key path as the gettext context, so reordering silently detaches every
translation attached to the entry.

---

## Commits

- No em dashes, no en dashes. CI greps them.
- Explain **why**, not just **what**. The diff already shows what.
- One logical change per commit.

---

## Questions

Open an issue before writing code for anything that touches the architecture rules
above. It is much cheaper to disagree about direction before the diff exists.