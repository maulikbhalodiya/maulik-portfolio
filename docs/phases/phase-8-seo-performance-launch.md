# PHASE 8 · SEO, performance, launch readiness

**Status: NOT STARTED** · depends on everything

The site is not going live yet. This phase makes it *ready* to go live, which is
a different thing.

---

## 8.1 noindex, and making it impossible to leave on

- [ ] `noindex, nofollow` on staging
- [ ] **Gated so it cannot survive into production.** Options: `WP_DEBUG` only,
      or an explicit opt-in constant that defaults off
- [ ] A comment in `inc/seo.php` explaining the trap, because the failure mode is
      invisible and a search engine will index whatever it sees first
- [ ] Verified it is actually emitting, not just present in the file

## 8.2 Structured data

- [ ] **Person schema** from the design's JSON-LD: name, job title, `alumniOf`
      Ganpat University, `worksFor` Qrolic Technologies, and the 10 `knowsAbout`
      entries
- [ ] **No email property.** No `sameAs` pointing anywhere that exposes an address
- [ ] Validated against schema.org, not just well-formed JSON
- [ ] Emitted from a schema block or a filtered `wp_head` hook, not hand-written
      JSON-LD pasted into a template

## 8.3 Per-route metadata

The design has 9 routes with their own titles and descriptions. All must survive.

- [ ] Per-route title and meta description
- [ ] Canonical on every page
- [ ] Open Graph including **`og:image`**, which the design is missing entirely
- [ ] `og:url`, `twitter:card`
- [ ] Favicon, `theme-color` meta, web manifest, apple-touch-icon. The design
      has none of these
- [ ] Sitemap, handled by RankKernel

## 8.4 Performance budget

| Metric | Target | Requirement |
|---|---|---|
| LCP | < 1.8s | 2.5s |
| INP | < 150ms | 200ms |
| **CLS** | **0.05 or better** | 0.1 |
| TTFB | < 400ms | 0.8s |
| TBT | < 100ms | heaviest Lighthouse weight |
| Lighthouse mobile | 95+ | |

Budgets: 150KB JavaScript, 50KB CSS, two web fonts maximum, zero jQuery.

- [ ] **LCP element is eager-loaded** with `fetchpriority="high"`, and
      `decoding="sync"`. Core lazy-loads images by default, and a lazy LCP image
      alone can push LCP past 2.5s
- [ ] **Intrinsic `width` and `height` on every image.** Most portfolio CLS comes
      from images, not fonts
- [ ] `sizes` attribute matches the real CSS layout
- [ ] Both asset filters verified active
- [ ] No third-party font request
- [ ] `no_found_rows => true` on any query that does not paginate
- [ ] No N+1 queries in any template
- [ ] Lighthouse measured on the homepage, a case study, and the archive

## 8.5 Cache correctness

- [ ] **Zero `is_user_logged_in()` branches in templates.** Full page caching
      cannot work for authenticated users, and a portfolio has no reason to
      produce per-user front-end output. This is a design decision with a
      performance consequence, and it is free.
- [ ] Deterministic output: no stray nonces, no random IDs, no per-request
      timestamps in cached HTML
- [ ] Stable asset versioning so cached HTML points at immutable URLs
- [ ] No cookies set on front-end GETs

## 8.6 RankKernel independence

- [ ] RankKernel active on the site
- [ ] **Theme has no code dependency on it.** No `class_exists`, no conditional
      rendering
- [ ] Deactivating RankKernel degrades SEO features and nothing else
- [ ] Verified by deactivating it and loading every page

## 8.7 Release blockers

- [ ] `screenshot.png`, 1200x900, real screenshot not a placeholder
- [ ] `readme.txt` current, correct `Stable tag`
- [ ] Theme Check **0 errors**. Three or more distinct issues can get a theme
      closed
- [ ] `Version:` bumped in `style.css` and `readme.txt`
- [ ] Release ZIP built and smoke-tested: required files present, forbidden
      entries absent
- [ ] Comma-separated author attribution, the format Theme Check expects

---

## Definition of done

- [ ] `noindex` emitting on staging and impossible to leave on
- [ ] Person schema valid, no email property
- [ ] All 9 routes with correct metadata, canonical, OG including image
- [ ] Favicon, theme colour, manifest present
- [ ] CLS 0.05 or better, LCP under 1.8s
- [ ] Lighthouse mobile 95+
- [ ] Zero user-conditional output in templates
- [ ] RankKernel deactivation degrades gracefully
- [ ] Theme Check 0 errors
- [ ] Release ZIP built and smoke-tested
- [ ] All CI gates green

**Commit:** `chore(release): v0.2.0 launch candidate`
