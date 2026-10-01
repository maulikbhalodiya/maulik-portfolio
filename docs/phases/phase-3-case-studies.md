# PHASE 3 · Case studies

**Status: NOT STARTED** · depends on Phase 2

The heart of the site. Four complete case studies, four lighter entries, one
deleted. The content is verified and written; this phase is about structure and
the resume enrichment.

---

## 3.1 Content model

- [ ] **Projects are pages with nested blocks. No custom post type, no
      taxonomy, no custom block type.**
- [ ] `templates/page-project.html`, the 12-section case study
- [ ] `page-projects.html` as the parent, `single.html` as the fallback
- [ ] Content stays editable in the Site Editor forever, with no plugin
      dependency

### Why no CPT, stated once so nobody re-litigates it

WordPress.org forbids custom post types, taxonomies and custom blocks in themes.
We are not submitting, but building to that standard produces better
architecture. The designer's own blueprint in the data file proposed a `project`
CPT with four custom dynamic blocks. That is a plugin architecture, and its
field list also omits `architectureFlows`, which the architecture diagram
renders, so it would have broken on first use.

The instinct of separating content from presentation was right. A CPT is the
wrong mechanism under a theme-only constraint. Nested blocks give the same
separation with no plugin territory.

The cost is that we cannot query projects by taxonomy. We do not need to. There
are eight. The archive filter is server-side links, which research confirms is
strictly better for Core Web Vitals than a client-side filter and works without
JavaScript.

## 3.2 The 12 sections

| # | Section | Heading | Source |
|---|---|---|---|
| 01 | Overview | What the system is | `overview` |
| 02 | Problem | Technical requirement and challenge | `problem` |
| 03 | My Role | Verified responsibilities | `myRole[]` |
| 04 | Technical Approach | Implementation direction | `technicalApproach` |
| 05 | Architecture | System Architecture Visualization | nodes + flows |
| 06 | Engineering Decisions | Key Architectural Decisions | `engineeringDecisions[]` |
| 07 | Implementation | Relevant Implementation Details | `implementationDetails[]` |
| 08 | Security | Verified Security and Validation Controls | **derived from flows** |
| 09 | Verification | Testing, Validation and Edge Case Checks | `verification[]` |
| 10 | Technologies | plain join | `technologies[]` |
| 11 | Confidentiality | fixed prefix + notice | `confidentialityNotice` |
| 12 | Related Work | Continue Exploring Engineering Case Studies | `relatedSlugs[]` |

- [ ] **There is no Impact section and no metrics.** The design deliberately
      claims no outcomes because outcomes need numbers. Do not add one.
- [ ] **Section 08 has no source field.** It derives from each flow's validation
      note, so security claims cannot drift from the architecture they describe.
      **Preserve the derivation.**
- [ ] Section 11 renders a fixed prefix plus the project's own notice

## 3.3 Four complete case studies, enriched from the resume

The design prototype describes this work abstractly. The resume is far more
specific, and every detail is verified. A named gateway and a first-of-kind
integration are far more convincing than an abstraction, and both are real.

### Cross-Domain Payment Architecture
- [ ] **Gateways named:** Checkout.com, ConvesioPay, TailoredPay
- [ ] **Three Gravity Forms add-ons built where none existed.** Specifically:
      Checkout.com webhook support plus a modern components UI rather than
      legacy frames; the first Gravity Forms integration for ConvesioPay, built
      from API documentation; the first WordPress integration of any kind for
      TailoredPay
- [ ] Three-D Secure flows, synchronous payment handling, signed callbacks,
      duplicate checks
- [ ] The business constraint stated plainly: enabling payments on domains not
      approved to hold them, with no redirect and no loss of trust
- [ ] 5 architecture nodes, 4 flows, 3 engineering decisions

### Employee Application Management
- [ ] Ten or more AJAX filter parameters
- [ ] Approve and reject with automated Brevo notification
- [ ] Internal notes, activity logging
- [ ] mPDF generation
- [ ] One codebase serving both Forminator and Gravity Forms
- [ ] 4 nodes, 3 flows, 2 decisions

### Secure Data Encryption
- [ ] Encryption key stored **outside the WordPress root**
- [ ] LatePoint booking notes, messages and custom fields
- [ ] 4 nodes, 3 flows, 2 decisions
- [ ] **No HIPAA claim.** See 3.6

### Identity Document OCR
- [ ] Passport and identity documents specifically
- [ ] Server-side processing with asynchronous polling
- [ ] 4 nodes, 4 flows, 2 decisions

## 3.4 Four lighter entries

Verified, valid, but only 2 nodes and 1 flow each. They read as summaries
rather than case studies, which is the honest level of detail available. Do not
inflate them.

- [ ] **Brevo API Automation**: multi-language template selection, PDF
      attachments, event-driven attribute changes on payment status and
      application stage
- [ ] **Object Storage Automation**: Hetzner, S3-compatible, scheduled
      Gravity Forms file migration on WP-Cron
- [ ] **Distance Based Dynamic Pricing**: Google Maps Distance Matrix API,
      per-mile billing beyond a free allowance, Stripe Payment Intents, admin
      approval before charge
- [ ] **WooCommerce Integration**: hook-driven extension over template
      overrides, upgrade-safe

## 3.5 Deleted

- [ ] **Rank Math SEO Engineering: removed entirely.**

The site runs RankKernel. He never built Rank Math. The design's own copy had
to concede the confusion on the page: "Maulik Bhalodiya did not create the Rank
Math plugin." Carrying a case study for a competitor's plugin on a site running
its own rival plugin was a liability. It also had 13 literal
`[Verified Placeholder: ...]` strings rendering in the problem, approach, role
and verification fields.

## 3.6 Never appears

- [ ] **No client names**, including the two in the resume. The design anonymises
      all eight projects consistently, and naming one while hiding seven invites
      questions about the others
- [ ] **No HIPAA-aligned claim.** A regulatory compliance claim we cannot
      verify, and it invites exactly the scrutiny a portfolio citation should
      not attract. The verifiable version, field-level authenticated encryption
      with the key outside the WordPress root, is strong on its own
- [ ] No impact section, no metrics, no percentages, no testimonials
- [ ] No skill ratings, stars, or progress meters. The `isPlaceholderScope` flag
      was the design's placeholder mechanism and it is gone with Rank Math
- [ ] No em or en dashes
- [ ] "Qrolic", never "Qrologic"

## 3.7 Architecture diagram

- [ ] Nested blocks with real `<button>` elements, no canvas
- [ ] Node grid, click to inspect, detail drawer
- [ ] Flow tabs, one per flow stage, with its own panel
- [ ] **Key flows by node `id`, not by label string.** The design matched
      `architectureFlows[].fromNode` against `architectureNodes[].label` for
      string equality, which breaks the moment a label is reworded
- [ ] Roving tabindex, arrow key navigation
- [ ] `aria-controls` pointing at real panel ids

## 3.8 Engineering decisions

- [ ] `engineeringDecisions[]` renders as a 3-column card grid
- [ ] Each is `{ title, rationale }`, the only label plus body structure in the
      model
- [ ] **"Rejected options" is not a structured field.** Two of the nine decisions
      name an alternative in the title, using the word "Over". The other seven
      do not. If naming the rejected option matters it must be written, and it
      must be written as real recollection, not inferred. Inferring an
      alternative for the other seven would be exactly the kind of unverified
      claim this site forbids.

---

## Definition of done

- [ ] `page-project.html` template rendering 12 sections
- [ ] All 8 case studies as pages, no CPT
- [ ] 4 complete studies carry the resume's named detail
- [ ] Rank Math absent
- [ ] Section 08 derived from flow validation notes
- [ ] Architecture diagram keyed by node id, keyboard operable
- [ ] Zero client names, zero HIPAA claim, zero dashes
- [ ] axe-core clean on the case study template
- [ ] All CI gates green

**Commit:** `feat(projects): 8 case studies with architecture diagrams`
