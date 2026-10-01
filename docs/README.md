# docs

Per-phase to-do lists for the `maulik-portfolio` WordPress block theme.

Each file is the working checklist for that phase. Tick items as they land, and
keep the **Status** line at the top current.

---

## Phases

| Phase | Scope | Status | File |
|---|---|---|---|
| 0 | Repository foundation | **COMPLETE** | [phase-0-foundation.md](phases/phase-0-foundation.md) |
| 1 | Design foundation | NOT STARTED | [phase-1-design-foundation.md](phases/phase-1-design-foundation.md) |
| 2 | Shell and homepage | NOT STARTED | [phase-2-shell-homepage.md](phases/phase-2-shell-homepage.md) |
| 3 | Case studies | NOT STARTED | [phase-3-case-studies.md](phases/phase-3-case-studies.md) |
| 4 | Remaining pages | NOT STARTED | [phase-4-pages.md](phases/phase-4-pages.md) |
| 5 | The three greenfield items | NOT STARTED | [phase-5-greenfield.md](phases/phase-5-greenfield.md) |
| 6 | Components and motion | NOT STARTED | [phase-6-components-motion.md](phases/phase-6-components-motion.md) |
| 7 | Accessibility | NOT STARTED | [phase-7-accessibility.md](phases/phase-7-accessibility.md) |
| 8 | SEO, performance, launch readiness | NOT STARTED | [phase-8-seo-performance-launch.md](phases/phase-8-seo-performance-launch.md) |
| 9 | GitHub and ongoing maintenance | PARTIAL | [phase-9-github-maintenance.md](phases/phase-9-github-maintenance.md) |

---

## Critical path

```
Phase 1 tokens ──> Phase 2 shell ──> Phase 3 case studies ──> Phase 4 pages
       │                                                          │
       └────────────> Phase 5 greenfield <────────────────────────┘
                                   │
                              Phase 6 components
                                   │
                              Phase 7 accessibility
                                   │
                              Phase 8 launch readiness
```

**Phase 1 blocks everything.** Every page reads those tokens, so a palette
correction after Phase 3 means rechecking every page.

**Phase 5 is the schedule risk** because none of it is a conversion. The contact
form, the server-generated résumé and the scroll reveals do not exist in the
design prototype in any form.

---

## Working rules

**Every phase ends with a commit and green gates.** No phase starts until the
previous one is committed, independently verified, and pushed.

**Subagent lanes run in parallel only when their write scopes do not overlap.**
Overlapping writers is the fastest way to lose work.

**Verification is never self-certified.** Every claim is checked with an
independent command, not the subagent's own summary. Two documents in this
project were wrong and neither was visible without checking.

**Gates, all blocking:**

| Gate | Tool | Bar |
|---|---|---|
| PHP standards | `phpcs` WordPress-Extra + Docs | 0 errors, 0 warnings |
| Static analysis | PHPStan level 5 | 0 errors |
| Syntax | `php -l` | 0 errors |
| CSS | stylelint | clean |
| JS | eslint | clean |
| Token drift | regenerate and diff | no drift |
| WP compliance | Theme Check | 0 errors |
| Accessibility | axe-core, every template | 0 violations |
| Content rules | grep: dashes, mailto, raw email | 0 hits |

---

## Related documents

These live in the parent directory and are reference material, not checklists.

| Document | Contents |
|---|---|
| `PLAN.md` | Reasoning. What we are building and why |
| `ROADMAP.md` | Build order, condensed |
| `DESIGN-SPEC.md` | Colour, type, texture, motion, layout tokens |
| `CONTENT-INVENTORY.md` | All case studies verbatim, timeline, skills, RankKernel |
| `PORT-ANALYSIS.md` | Components, pages, accessibility audit, compliance |
| `RESEARCH-02` | Block theme architecture, `theme.json` v3 |
| `RESEARCH-03` | Data modelling, Core Web Vitals, caching |
| `RESEARCH-04` | WPCS, build pipeline, release, security |

---

## Open decisions

Not blocking Phase 1. Blocking later phases.

- [ ] **Real email address.** Needed for the résumé filename and Person schema.
      Placeholder until then, and the form is the only contact path either way
- [ ] **Phone number.** Not published on the site
- [ ] **`Distance Based Dynamic Pricing` title.** Client pricing logic, not a
      service, but the wording could read commercially to a skimming recruiter.
      Not on the homepage
- [ ] **LinkedIn URL trailing-dash discrepancy**, to verify
- [ ] **Real RankKernel repository URL.** The site currently points at a profile
- [ ] **Whether the standalone résumé document may carry a client reference
      line.** The site may not, the document may
