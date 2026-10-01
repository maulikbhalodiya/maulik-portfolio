# PHASE 5 · The three greenfield items

**Status: NOT STARTED** · depends on Phase 4

**None of this is a port.** None of it exists in the design prototype. This is
new construction and it is the schedule risk in the whole project, because
everything else is largely markup conversion.

---

## 5.0 What the prototype actually contains

Verified by exhaustive grep of the entire `src/` tree:

| Thing | Found |
|---|---|
| `<form>` | **zero** |
| `onSubmit` | **zero** |
| `<select>` | **zero** |
| `honeypot` | **zero** |
| `fetch(` / `XMLHttpRequest` | **zero** |
| `/wp-json` / `admin-post.php` | **zero** |
| `IntersectionObserver` | **zero** |
| `whileInView` / `animate-*` / reveal classes | **zero** |
| imports of the `motion` package | **zero** |

The contact page is four clickable cards and a static SVG. There is no form. The
`motion` package is in `package.json` and imported by no file, so **there is no
scroll-reveal animation anywhere in the design.** The résumé download is a
template literal wrapped in a `Blob`, and the generated file does not match the
site design at all.

Three items. All new.

## 5.1 Contact form

The largest single piece of new work.

- [ ] **`core/form`**, which WordPress 6.5 and later ship natively. It is the
      right tool and it is theme-compatible.
- [ ] **Intent dropdown.** The design never had one, and the original spec called
      for it. This is the reason a form exists instead of an email link, so it
      needs to be genuinely useful, not decorative.
- [ ] Server-side handler on `admin_post`
- [ ] `wp_nonce_field` and `check_admin_referer` on submit
- [ ] Capability check where relevant
- [ ] **Honeypot field**, hidden from humans, checked server side
- [ ] Input validation and sanitization, `wp_unslash()` always before sanitizing
- [ ] All output escaped per context
- [ ] **No raw email in any response, log, or error message**
- [ ] Success and error states, both announced
- [ ] **Works without JavaScript.** A form that requires JS to submit is broken
- [ ] Rate limiting or at least a submission timestamp guard

### Where the email actually goes

The address is a placeholder until the user supplies it. The form must work
before then, by routing to a filterable hook, so that setting the real address is
a configuration change and not a code change.

## 5.2 Résumé download

- [ ] Server-generated file, replacing the client-side `Blob` templating
- [ ] The prototype's generated HTML has hardcoded hex and a system font stack, so
      **the downloaded document does not look like the site**
- [ ] Filename without a real email address
- [ ] Matches the site design, using the same tokens
- [ ] Print stylesheet correct, so `window.print()` produces a clean document

## 5.3 Scroll reveals

- [ ] **One mechanism, applied once.** Not a per-component animation library
- [ ] Fade and rise, `transform` and `opacity` only
- [ ] **Never animate layout properties.** `width`, `height`, `top`, `margin`,
      `font-size` force synchronous layout every frame and inflate both TBT and
      CLS
- [ ] Fires **once** on entering the viewport, via `IntersectionObserver`
- [ ] **`prefers-reduced-motion` respected**, meaning elements are simply visible
      with no transition
- [ ] Unobserve after firing, so there is no re-animation on scroll up
- [ ] Content visible if JavaScript never runs, via a class on `<html>` that JS
      removes

This is the single highest-value addition available. The design's restrained
aesthetic currently has no entrance motion across eight long sections, which
makes the page feel static. One consistent mechanism fixes that for a few
kilobytes.

---

## Definition of done

- [ ] Contact form submits server-side with nonce and honeypot
- [ ] Intent dropdown present and useful
- [ ] No raw email anywhere in responses or logs
- [ ] Form works with JavaScript disabled
- [ ] Résumé download is server-generated and matches the design
- [ ] Scroll reveals on one mechanism, transform and opacity only
- [ ] `prefers-reduced-motion` fully respected
- [ ] Content visible without JavaScript
- [ ] All CI gates green

**Commit:** `feat(forms): contact form, server-generated resume, scroll reveals`
