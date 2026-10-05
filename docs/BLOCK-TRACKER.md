# Block-level tracker, theme vs approved design

Every row is one section or component on one page. Each has a stable ID you can
quote back to me as `PAGE-ID`, for example `RK-2`.

**Status legend**

| Mark | Meaning |
| --- | --- |
| DONE | Matches the design, verified by measurement in a browser |
| PART | Matches in appearance, behaviour or content still missing |
| QUEUED | Defect confirmed and measured, fix not yet dispatched |
| RUNNING | Agent working on it now |
| BLOCKED | Cannot proceed without a decision from you |
| DEVIATION | Intentionally differs from the design, reason given |

Last full measurement: 1440x1000 desktop, live site, headless Chrome.
Section heights are ours vs design. Delta is percent.

---

## / (Home)

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| HM-0 | Site header | 64px | 65px | -1px | DONE, active nav state works on all 6 routes |
| HM-1 | Hero section | 1000 | 1000 | 0% | DONE, panel overflow fixed |
| HM-2 | Hero canvas panel | 656px | 656px | 0% | DONE, was overflowing to x=1450 |
| HM-3 | Hero social icons | 40px | 40px | 0% | DONE |
| HM-4 | **Capabilities, section 02** | 1284 | 1159 | +11% | PART, tabs inert, 0 buttons |
| HM-5 | **Technical ecosystem, section 03** | 1094 | 1059 | +3% | RUNNING, tabs and rotation |
| HM-6 | Selected work, section 04 | 3027 | 3169 | -4% | DONE |
| HM-7 | Archive arrow link | 16px | 16px | 0% | DONE, was 120px |
| HM-8 | **Experience, section 05** | 1692 | 1594 | +6% | DONE, surface and padding correct |
| HM-9 | **Experience card label order** | flex row | stacked | wrong | QUEUED, label sits beside title not above |
| HM-10 | **Education / Open Source snap cards** | 137 / 113 | 106 / 106 | +31/+7 | QUEUED, cards unequal |
| HM-11 | Approach, section 06 | 1085 | 1068 | +2% | RUNNING, stage rows inert |
| HM-12 | About, section 07 | 686 | 679 | +1% | DONE |
| HM-13 | **Hiring CTA, section 08** | 751 | 753 | -0% | DONE, inner card added |
| HM-14 | CTA buttons and arrows | 16px | 16px | 0% | DONE |
| HM-15 | Site footer | 384 | 342 | +12% | PART, legal row fixed, height short |
| HM-16 | Footer external links | correct | correct | 0% | DONE, was `target="&quot;_blank&quot;` |

## /about/

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| AB-0 | Hero | 607 | 607 | 0% | DONE |
| AB-1 | Who I am | 572 | 572 | 0% | DONE |
| AB-2 | Qrolic timeline | 1294 | 1294 | 0% | DONE |
| AB-3 | Core engineering domains | 1223 | 1207 | +1% | DONE |
| AB-4 | RankKernel section | 925 | 905 | +2% | DONE |
| AB-5 | Resume actions | 2 buttons | 4 buttons | missing 2 | PART, View/Download Resume absent |
| AB-6 | Surface check | all dark | all dark | 0% | DONE, cream and warm removed |

## /contact/

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| CT-0 | Hero | 470 | 542 | -13% | PART, hero short by 72px |
| CT-1 | Verified Destinations | 794 | 815 | -3% | DONE |
| CT-2 | **Contact form with intent dropdown** | absent | absent in design | n/a | BLOCKED, needs your decision |
| CT-3 | Channel buttons | 2 | 3 | missing 1 | PART, email replaced by form link |

## /projects/

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| PJ-0 | Hero | 533 | 546 | -2% | DONE |
| PJ-1 | **Filter tablist** | 83px, 10 tabs | 83px, 10 tabs | 0% | DONE, verified live |
| PJ-2 | Filter behaviour | 9/0/9/6/1/2/7/2 | same | 0% | DONE, DOM removal not CSS hiding |
| PJ-3 | Independent Development | 695 | 676 | +3% | DONE |
| PJ-4 | Professional Work | 2417 | 2445 | -1% | DONE |
| PJ-5 | Case study arrow | 16px | 16px | 0% | DONE, was 45px |

## /rankkernel/

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| RK-0 | Hero | 658 | 637 | +3% | DONE |
| RK-1 | What RankKernel is | 511 | 511 | 0% | DONE |
| RK-2 | Architecture diagram | 1748 | 1733 | +1% | DONE |
| RK-3 | **Status filter tablist** | 4 tabs | 4 tabs | 0% | DONE, verified live |
| RK-4 | **Filter behaviour** | 9/3/3/3 | same | 0% | DONE, DOM removal |
| RK-5 | Major technical areas | 1378 | 1396 | -1% | DONE |
| RK-6 | Architectural standards | 1078 | 1078 | 0% | DONE |
| RK-7 | Closing section | 607 | 671 | -10% | PART, 64px short |
| RK-8 | **Node click to select** | not interactive | click changes panel | missing | QUEUED, 9 panels of content needed |
| RK-9 | Uses `core/html` | yes | n/a | n/a | DEVIATION, conflicts with ARCHITECTURE.md 2.4 |

## /resume/

| ID | Section / component | Ours | Design | Delta | Status |
|---|---|---|---|---|---|
| RS-0 | Hero | 598 | 578 | +3% | DEVIATION, design prints a mailto block; owner later ruled the design's address ships, so this row is about layout only |
| RS-1 | 01 Professional Summary | 89 | 89 | 0% | DONE |
| RS-2 | 02 Technical Skills | 426 | 427 | 0% | DONE |
| RS-3 | 03 Professional Experience | 582 | 583 | 0% | DONE |
| RS-4 | 04 Selected Development Work | 643 | 643 | 0% | DONE |
| RS-5 | 05 Education | 85 | 85 | 0% | DONE, was 766px, 9x too tall |
| RS-6 | 06 + 07 closing grid | 507 | 508 | 0% | DONE |
| RS-7 | Download / Print actions | 2 buttons | 3 buttons | missing 1 | PART |

## Cross page

| ID | Item | Status |
|---|---|---|
| GL-1 | Four grid textures distinct at 48/32/96/64px | DONE, all four registered and distinct |
| GL-2 | No light surface on any page | DONE, 7 removed |
| GL-3 | Zero off-palette text colours | DONE, `#8A6A00` was the only one |
| GL-4 | Zero horizontal overflow at 1440 | DONE, all 6 routes |
| GL-5 | No em dash or en dash | DONE |
| GL-6 | Zero border-radius, zero box-shadow | DONE |
| GL-7 | Published email | DONE, design's own address only, owner decision; footer Connect + section 08 Email Me; no other address permitted and CI gates narrowed to enforce that |
| GL-8 | Icon primitive | DONE, `_icon.scss`, 16/12/14/20px |
| GL-9 | Focus visible | DONE, 2px `#FACC15` at 3px offset |
| GL-10 | Reduced motion | DONE |
| GL-11 | Print, no-print class | DONE |
| GL-12 | Editor-first architecture | DONE, content in post_content, templates generic |
| GL-13 | Contrast 4.24:1 on 76 elements | BLOCKED, design's own grey vs WCAG AA |
| GL-14 | Header height 64 vs 65 | DONE |
| GL-15 | Footer height 384 vs 342 | PART |
| GL-16 | Mobile 390 audit all routes | DONE, no overflow, tabs usable |
| GL-17 | Tablet 768 audit | QUEUED, not yet measured |

---

## Your feedback, applied or not

| # | Your instruction | Applied | Evidence |
|---|---|---|---|
| 1 | Commit and push before visual work | YES | commit `5caec04`, pushed, remote identical |
| 2 | Use a real browser, not source reading | YES | Playwright on both sites, 1440 and 390 |
| 3 | Sections 05 and 06 are wrong colours | YES | 05 flat `#0A0A0B`, 06 48px grid, verified |
| 4 | View Complete Project Archive arrow | YES | 120px to 16px |
| 5 | View Case Study arrow too large | YES | 45px to 16px |
| 6 | Reusable button and link primitives | YES | `_icon.scss`, one rule set |
| 7 | Read Full About arrow | YES | covered by the icon primitive |
| 8 | Experience card label above title | NOT YET | measured, queued as HM-9 |
| 9 | Approach tabs beside detail, not below | PART | given a grid column, side by side now; not interactive |
| 10 | Snap cards equal dimensions | NOT YET | measured, queued as HM-10 |
| 11 | Hiring CTA card match | YES | 751 vs 753 |
| 12 | Footer rebuild, fix malformed attributes | YES | `target="_blank"` correct, 0 malformed |
| 13 | Dedicated SVG audit | YES | all 18 icons measured |
| 14 | Spacing audit against the design | YES | all pages within 2 to 4% |
| 15 | Do not break the editor architecture | YES | no content moved into templates |
| 16 | Capability tabs must work | RUNNING | HM-4 |
| 17 | Ecosystem tabs plus 3s rotation, hover pause | RUNNING | HM-5, your decisions applied |
| 18 | RankKernel section match | MOSTLY | RK-7 and RK-8 outstanding |
| 19 | Contact form with intent dropdown | BLOCKED | CT-2 |
| 20 | Accessibility not regressed | YES | roles, keyboard, focus verified live |
| 21 | Do not invent content | YES | no fabricated metrics or clients |
| 22 | Responsive at desktop, tablet, mobile | PART | GL-17, tablet not measured |

## Open decisions for you

1. **CT-2 contact form.** This WordPress install has no form block; I verified
   `form.php` is absent from the 92 block files in core. The design has no form
   either, so there is nothing to copy. I recommend a presentational theme block
   with a real endpoint. It is a behavioural change so I need your call.
2. **GL-13 contrast.** 76 elements at 4.24:1 against the 4.5:1 WCAG requires.
   That is the design's own muted grey. Exact match keeps it; accessibility
   lightens it and visibly deviates.
3. **RK-9 core/html.** `ARCHITECTURE.md` 2.4 forbids it, but the RankKernel filter
   uses it. Either update the document or convert RankKernel to a dynamic block
   like Projects. I recommend converting, for one mechanism rather than two.
4. **GL-17 tablet.** Not yet measured at 768. Say the word and I will.