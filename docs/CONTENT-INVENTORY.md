# CONTENT INVENTORY
### Complete content model extracted from `portfolioData.ts`
### Everything here is verified content, ready to become page copy.

---

## 0. CRITICAL STRUCTURAL FINDING

**The design does NOT use the CASE / Pro-AI-R framework.** There is no
`challenge` / `approach` / `solution` / `impact` structure anywhere. The earlier
spec assumed one; the actual design uses a different and better framework.

**More importantly: there is no `solution` field and no `impact` field.** The
design deliberately does not claim outcomes, because outcomes would require
metrics. This is a compliance strength, not a gap. Do not add an impact section
with invented numbers to satisfy an earlier framework.

The real structure is 12 numbered sections, verified in `ProjectCaseStudyPage`:

| # | Label | Heading | Source field |
|---|---|---|---|
| 01 | Overview | What the system is | `overview` |
| 02 | Problem | Technical requirement and challenge | `problem` |
| 03 | My Role | Verified responsibilities | `myRole[]` |
| 04 | Technical Approach | Implementation direction | `technicalApproach` |
| 05 | Architecture | System Architecture Visualization | `architectureNodes[]` + `architectureFlows[]` |
| 06 | Engineering Decisions | Key Architectural Decisions | `engineeringDecisions[]` |
| 07 | Implementation | Relevant Implementation Details | `implementationDetails[]` |
| 08 | Security | Verified Security and Validation Controls | **derived from `architectureFlows[]`** |
| 09 | Verification | Testing, Validation and Edge Case Checks | `verification[]` |
| 10 | Technologies | (plain join) | `technologies[]` |
| 11 | Confidentiality | (fixed prefix + notice) | `confidentialityNotice` |
| 12 | Related Development Work | Continue Exploring Engineering Case Studies | `relatedSlugs[]` |

Section 08 Security has **no source field at all**. It is computed from
`architectureFlows[].securityOrValidation` at render time. Elegant, and it means
security claims can never drift from the actual flow descriptions.

### 0.1 "Rejected options" is NOT a structured field

The earlier spec required naming the rejected option in each case study. The
design does not have that structure. It is only implied in **two** engineering
decision titles, which happen to use the word "Over":

- `HMAC SHA 256 Payload Signing Over Simple API Keys`
- `Hook Driven Extension Over Template Overrides`

Seven other decisions have no rejected-option framing. If naming the rejected
option matters, it must be added as a field. It is not free to invent, and
inferring an alternative for the other seven would be exactly the kind of
unverified claim the site forbids.

---

## 1. THE DATA MODEL (7 interfaces, verbatim)

```ts
interface ProjectCaseStudy {
  id: string; number: string; slug: string; title: string;
  workClassification: 'Professional Project' | 'Personal Project';
  category: string; filterCategories: string[];
  shortDescription: string; technologies: string[];
  featured: boolean; isPlaceholderScope?: boolean;
  overview: string; problem: string; myRole: string[];
  technicalApproach: string; architectureSummary: string;
  architectureNodes: ArchitectureNode[];
  architectureFlows: ArchitectureFlowStep[];
  engineeringDecisions: { title: string; rationale: string }[];
  implementationDetails: string[]; verification: string[];
  confidentialityNotice: string; relatedSlugs: string[];
}
```

Also: `CapabilityItem` (5 entries), `EcosystemCategory` (7 entries),
`RankKernelSubsystem` (9 entries), `ArchitectureNode`, `ArchitectureFlowStep`,
`ExperienceItem` (2 entries), `EngineeringStage` (5 entries).

### 1.1 Fields that do NOT exist on any project

| Absent field | Consequence |
|---|---|
| `client` | All 9 projects are anonymized by design. Correct. |
| `liveUrl` | No project links anywhere. |
| `repoUrl` | No project links to code. |
| `year` / `date` | **No project has a date at all.** |
| `metrics` | **No numeric metric field exists on any project.** |
| `status` | No status field. Derived from `isPlaceholderScope` + `featured`. |
| `impact` | Deliberately absent. |

**This is the single most important compliance fact in this document.** The
design has no metrics, no percentages, no outcome claims, and no client names.
There is nothing to strip. The no-invention rule is satisfied structurally, not
by vigilance.

---

## 2. NINE PROJECTS

All 9 are `workClassification: 'Professional Project'`. The union permits
`'Personal Project'` but nothing uses it, because RankKernel deliberately lives
outside `PROJECTS_DATA`.

| # | slug | Title | featured | placeholder |
|---|---|---|---|---|
| 01 | `cross-domain-payment-architecture` | Cross Domain Payment Architecture | yes | no |
| 02 | `employee-application-management` | Employee Application Management System | yes | no |
| 03 | `identity-document-ocr` | Identity Document OCR Integration | yes | no |
| 04 | `wordpress-data-encryption` | Secure WordPress Data Encryption | yes | no |
| 05 | `rank-math-seo-engineering` | Rank Math SEO Engineering | yes | **yes, extensively** |
| 06 | `brevo-api-automation` | Brevo API Automation | no | flag set, prose written |
| 07 | `distance-based-dynamic-pricing` | Distance Based Dynamic Pricing | no | flag set, prose written |
| 08 | `object-storage-automation` | Object Storage Automation | no | flag set, prose written |
| 09 | `woocommerce-integration` | WooCommerce Integration | no | flag set, prose written |

**Four are fully written** (01 to 04) with complete architecture nodes, flows,
decisions, implementation details and verification. **Five carry the
`isPlaceholderScope` flag**, but only 05 actually has placeholder text.

### 2.1 Project 05 is a liability, decide before build

`rank-math-seo-engineering` contains **13 literal placeholder strings** in the
rendered output, including in `problem`, `technicalApproach`, `myRole[0]`,
`implementationDetails[]` and `verification[]`. Examples as they would appear on
the page:

- `[Verified Placeholder: Specific technical requirement for Rank Math SEO engineering work to be inserted once public disclosure details are confirmed.]`
- `[Verified Placeholder: Detailed architectural approach, WordPress filter hooks and PHP implementation details will be populated using verified project documentation.]`
- `Placeholder for verified testing and validation methodology`

It also carries this in its own `overview`, which is good practice but reads
oddly as portfolio copy:

> 'Note: Maulik Bhalodiya did not create the Rank Math plugin.'

And in `myRole[1]`:

> 'Clarification: This work was completed as part of professional employment and does not imply creation or ownership of the Rank Math product'

**This is the one entry where the honesty machinery becomes visible clutter.**
Three options, all defensible:

1. **Ship it as-is.** Maximum transparency, worst reading experience. A
   recruiter sees a page full of bracket placeholders.
2. **Reduce it to a short verified entry.** One paragraph of what is genuinely
   known, no case study template, marked clearly as scope-protected. This matches
   the `isPlaceholderScope` intent.
3. **Remove it from the portfolio entirely.**

Recommendation: **option 2.** The `isPlaceholderScope` flag already exists and
renders "Verified Entry · Scope Protected" in the case study metadata panel, so
the design anticipated a lighter treatment. Right now it renders the full 12
section template anyway, which is the actual bug.

---

## 3. CASE STUDY CONTENT, VERBATIM

Full text for all four complete projects. This is publishable copy.

### 3.1 Cross Domain Payment Architecture (01)

**Category:** Payments and Security Architecture
**Technologies:** WordPress, PHP, Gravity Forms, REST APIs, Payment Gateways, HMAC SHA 256
**Filter tags:** Professional, WordPress, PHP, API, Payments, Security

**Short description:**
Reusable cross domain payment architecture featuring an approved payment
environment, iframe checkout interface, HMAC SHA 256 signed REST communication,
shared secret authentication and multi gateway webhook processing.

**Overview:**
Built a reusable cross domain payment system enabling multiple origin WordPress
websites to process transactions through a centralized, approved payment
environment. The architecture uses an embedded iframe payment interface paired
with cryptographically signed REST API requests and asynchronous webhook
callbacks.

**Problem:**
Origin websites needed to collect user form submissions via Gravity Forms and
process payments without directly hosting sensitive payment gateway credentials
or handling raw payment processing on the origin domain. Every cross domain
request required tamper proof verification and reliable transaction state
synchronization.

**My role:**
- Designed and implemented the custom WordPress plugin architecture on both the origin website and the centralized payment environment
- Built the REST API communication layer using HMAC SHA 256 request signing and shared secret authentication
- Integrated the iframe based payment interface with Gravity Forms submission workflows
- Implemented webhook receivers to synchronize payment statuses across multiple payment gateways back to the originating entry

**Technical approach:**
Separated the checkout lifecycle into two coordinated WordPress plugins: an
Origin Client Plugin integrated with Gravity Forms and a Central Payment Host
Plugin connected to multiple payment gateways. Whenever a user initiates payment
on the origin site, the origin plugin constructs a canonical payload, signs it
with HMAC SHA 256 using a shared secret and initializes a secure session on the
payment host, which renders inside an iframe interface.

**Architecture summary:**
Origin WordPress Site (Gravity Forms) · HMAC SHA 256 Signed REST Request ·
Approved Payment Environment (Iframe Interface) · Multi Gateway Processing ·
Signed Webhook Callback · Origin Entry Settlement.

**5 nodes:** Origin WordPress Site (Intake Layer) / HMAC SHA 256 Signer
(Security Layer) / Approved Payment Environment (Processing Host) / Multi Gateway
Router (Gateway Layer) / Signed Webhook Callback (Synchronization)

**4 flows:** Internal PHP Service / HTTPS REST API / Iframe + Gateway API /
Signed Webhook REST, each with its own security or validation note.

**3 engineering decisions:**
1. `HMAC SHA 256 Payload Signing Over Simple API Keys`: Static bearer tokens alone do not guarantee that transaction amounts or order identifiers were not modified in transit. Signing the canonical request payload with HMAC SHA 256 ensures both sender authenticity and payload integrity.
2. `Centralized Gateway Abstraction for Multiple Gateways`: By encapsulating gateway differences inside the approved payment environment, origin websites only need to implement a single standardized REST contract regardless of which underlying payment gateway processes the transaction.
3. `Asynchronous Webhook Reconciliation`: Browser redirects and iframe client messages can be interrupted if a user closes their tab. Server to server signed webhooks ensure the Gravity Forms entry on the origin site always receives the authoritative payment status.

**4 implementation details:**
- Custom REST endpoints registered via `register_rest_route` on both origin and payment host environments
- Cryptographic signature validation using PHP `hash_hmac` with sha256 and timing safe `hash_equals` checks
- Iframe communication coordinator handling checkout session rendering and completion feedback
- Gravity Forms entry meta persistence for transaction references, gateway identifiers and audit logs

**3 verification steps:**
- Verified signature rejection when any payload field, currency value or secret key is altered
- Tested asynchronous webhook delivery, retry idempotency and duplicate callback protection
- Validated end to end transaction state updates across multiple payment gateways in staging environments

**Confidentiality notice:**
Completed as part of my professional role at Qrolic Technologies. Client names,
domain URLs and proprietary gateway credentials have been omitted to protect
business confidentiality.

**Related:** `wordpress-data-encryption`, `identity-document-ocr`

### 3.2 Employee Application Management System (02)

**Category:** Workflow and Access Control
**Technologies:** WordPress, PHP, Gravity Forms, Forminator, AJAX, Custom Roles
**Filter tags:** Professional, WordPress, PHP, Automation

**Short description:**
Role based employee application management workflow built on WordPress, PHP,
Gravity Forms, Forminator and AJAX with application assignment, asynchronous data
loading and structured status transitions.

**Overview:**
Engineered an internal application management system within WordPress that
transforms form submissions from Gravity Forms and Forminator into structured
applicant records with role based assignment, asynchronous filtering and multi
stage review workflows.

**Problem:**
Review teams needed a centralized interface inside WordPress to assign incoming
employee applications to specific reviewers, restrict visibility based on
organizational roles and update application statuses without slow full page
reloads.

**My role:**
- Implemented custom WordPress user roles and granular capability checks governing application access
- Built the application assignment and status workflow engine in PHP
- Developed AJAX endpoints for asynchronous application table filtering, detail inspection and status updates
- Integrated form submission data from both Gravity Forms and Forminator into a unified review workflow

**Technical approach:**
Created a custom plugin layer that normalizes submission entries from Gravity
Forms and Forminator, attaches assignment and status metadata and exposes
capability gated AJAX endpoints so authorized reviewers can filter, assign and
progress applications dynamically.

**4 nodes, 3 flows, 2 decisions:**
1. `Server Enforced Role Scoping on AJAX Queries`: Rather than hiding unassigned applications only in the frontend UI, all database and entry queries are scoped on the server according to the logged in user role and capability level.
2. `Unified Adapter for Gravity Forms and Forminator`: Abstracting form plugin differences behind a consistent PHP data structure allowed the assignment and status interface to work seamlessly across both form systems.

**3 verification steps:**
- Verified unauthorized roles cannot view or mutate unassigned applications via direct AJAX calls
- Tested asynchronous filtering and status transitions across concurrent reviewer sessions
- Validated input sanitization and output escaping on all applicant profile fields

**Confidentiality notice:**
Completed as part of my professional role at Qrolic Technologies. Organization
details and internal applicant schemas have been anonymized.

**Related:** `identity-document-ocr`, `wordpress-data-encryption`

### 3.3 Identity Document OCR Integration (03)

**Category:** API Integration and Automation
**Technologies:** WordPress, PHP, Azure Form Recognizer, AJAX, Gravity Forms
**Filter tags:** Professional, WordPress, PHP, API, Automation

**Short description:**
Custom WordPress plugin integrating Azure Form Recognizer with Gravity Forms to
analyze uploaded identity documents via asynchronous AJAX polling and
automatically map extracted OCR data into form fields.

**Overview:**
Developed a custom WordPress plugin that connects Gravity Forms file uploads to
Microsoft Azure Form Recognizer. When a user uploads an identity document, the
plugin submits the document for OCR analysis, polls the asynchronous operation
status via AJAX and populates extracted identity fields directly into the form.

**Problem:**
Manual entry of identity document information into complex forms introduced
transcription errors and slowed user onboarding. Because cloud OCR analysis runs
asynchronously, sending a synchronous blocking request during form interaction
would cause timeouts and poor user experience.

**My role:**
- Built the custom WordPress plugin bridging Gravity Forms file upload fields with Azure Form Recognizer REST APIs
- Implemented the asynchronous polling workflow using WordPress AJAX endpoints and server side HTTP calls
- Developed the parser and field mapper that extracts structured OCR key value pairs and populates target Gravity Forms inputs
- Added validation and error handling for unsupported document formats, network timeouts and low confidence fields

**Technical approach:**
Designed a two phase asynchronous pipeline. First, when a document is uploaded,
a secure PHP AJAX handler validates the file and dispatches it to the Azure Form
Recognizer Analyze endpoint, returning an operation location identifier. Second,
the frontend initiates controlled AJAX polling against a status endpoint until
Azure completes extraction, at which point the PHP mapper normalizes the OCR
fields and returns only the necessary form values.

**4 nodes, 4 flows, 2 decisions:**
1. `Asynchronous AJAX Polling Instead of Blocking Requests`: Cloud OCR analysis takes variable time depending on document complexity. Asynchronous polling prevents PHP worker exhaustion and provides clear visual feedback to the user.
2. `Server Side Credential Isolation`: All communication with Azure Form Recognizer occurs through the WordPress PHP backend so subscription keys and internal OCR payloads remain protected.

**3 verification steps:**
- Verified accurate field population across supported identity document layouts
- Tested graceful fallback behavior when unreadable images or invalid file types are uploaded
- Confirmed Azure API keys and raw cloud diagnostic headers are never exposed in browser network responses

**Confidentiality notice:**
Completed as part of my professional role at Qrolic Technologies. Specific client
identity, document templates and Azure tenant details have been omitted.

**Related:** `employee-application-management`, `cross-domain-payment-architecture`

### 3.4 Secure WordPress Data Encryption (04)

**Category:** Security and Cryptography
**Technologies:** WordPress, PHP, Sodium, LatePoint, Encryption
**Filter tags:** Professional, WordPress, PHP, Security

**Short description:**
Field level encryption of sensitive LatePoint appointment data in WordPress using
PHP Sodium and an external encryption key, paired with capability gated
decryption for authorized roles.

**Overview:**
Implemented a cryptographic data protection layer for a WordPress booking system
using LatePoint. Sensitive customer appointment data is encrypted before database
storage using PHP Sodium and an externally stored encryption key, ensuring that
unauthorized database access or unprivileged user accounts cannot read sensitive
records.

**Problem:**
Standard WordPress and booking plugin tables store customer appointment notes and
sensitive personal details as plain text in MySQL. The requirement was to protect
sensitive appointment data at rest while allowing authorized staff roles to view
decrypted information inside the WordPress interface.

**My role:**
- Implemented authenticated symmetric encryption and decryption workflows using the PHP Sodium cryptography extension
- Configured external encryption key loading outside the WordPress database to separate ciphertext from key material
- Integrated encryption hooks into the LatePoint appointment creation and update lifecycle
- Built capability gated decryption logic so only users with verified WordPress capabilities can view plaintext records

**Technical approach:**
Intercepted sensitive appointment fields prior to database persistence, generated
unique cryptographic nonces per record and encrypted the payload using PHP Sodium
with an external key. When records are retrieved, the system checks the current
user capabilities before performing decryption.

**4 nodes, 3 flows, 2 decisions:**
1. `PHP Sodium Secretbox With Per Record Nonces`: Modern authenticated encryption in PHP Sodium protects both confidentiality and integrity, ensuring that ciphertext tampering is immediately detected during decryption.
2. `Separation of Key Material and Database Storage`: Storing the encryption key inside the same MySQL database as the encrypted records would defeat the purpose of encryption at rest. Loading the key externally ensures database backups alone cannot be decrypted.

**3 verification steps:**
- Verified database tables contain only encrypted ciphertext for protected appointment fields
- Tested decryption access across authorized and unauthorized WordPress user roles
- Confirmed corrupted or tampered ciphertext fails authentication cleanly without exposing sensitive errors

**Confidentiality notice:**
Completed as part of my professional role at Qrolic Technologies. Client details
and specific protected schema fields have been anonymized.

**Related:** `cross-domain-payment-architecture`, `employee-application-management`

### 3.5 Projects 06 to 09, structure verified but lighter

All four have valid `problem`, `myRole`, `technicalApproach`, decisions,
implementation details and verification, but with only 2 nodes and 1 flow each.
They read as verified summaries rather than case studies, which is the honest
level of detail available.

| slug | Category | Nodes | Flows | Decisions |
|---|---|---|---|---|
| `brevo-api-automation` | API and Marketing Automation | 2 | 1 | 1 |
| `distance-based-dynamic-pricing` | Backend Calculation and E Commerce | 2 | 1 | 1 |
| `object-storage-automation` | Cloud Storage and Automation | 2 | 1 | 1 |
| `woocommerce-integration` | E Commerce and Backend Systems | 2 | 1 | 1 |

The `distance-based-dynamic-pricing` decision is a good one:
`Authoritative Server Side Price Recalculation`: Never trust prices calculated
in browser JavaScript; always recompute totals on the PHP backend.

---

## 4. THE ARCHIVE FILTER IS BUGGY

The filter is client-side `useState` with no URL params and no links. Ten
options: `All, Independent, Professional, WordPress, PHP, API, Payment, Security,
Integration, WooCommerce`.

**Five real defects:**

1. **`Payment` matches nothing.** The button says `Payment`, the data says
   `Payments`. Special-cased to check both, so it works, but only via the
   special case.
2. **`Integration` matches nothing directly.** Special-cased to `Automation` or
   `API`. There is no project with the tag `Integration`.
3. **`Independent` always returns an empty professional grid.** It is
   special-cased to `[]`, so selecting it always shows the empty state
   *"No professional projects match the active filter (Independent)."* The
   RankKernel card above is controlled by a separate `showIndependent` boolean,
   so the intent works, but the message reads as a bug to a visitor.
4. **`Automation` and `Payments` have no filter button.** `Automation` is a real
   tag on projects 03, 06, 08 and `Payments` on 01. They are reachable only via
   `Integration` and `Payment`.
5. **The filter option list and the data vocabulary are simply different sets.**
   Options use 10 words, data uses 8, and they do not line up.

**Decision needed:** reconcile the filter vocabulary, or accept the special
cases. Either way this needs fixing, not porting as-is.

---

## 5. CAPABILITY AREAS (5)

| # | Title | Related project |
|---|---|---|
| 01 | WordPress Engineering | Employee Application Management System |
| 02 | PHP Backend | RankKernel Open Source Engine |
| 03 | API and Integration Engineering | Identity Document OCR Integration |
| 04 | Payment Engineering | Cross Domain Payment Architecture |
| 05 | Security | Secure WordPress Data Encryption |

Each has `summary`, `architecturalFocus`, 4 `keyMechanisms`, 7 `technologies`.
**No numeric values, no proficiency levels, no percentages.** Correct.

Note capability 02 links to `rankkernel`, which is not a project slug. It is
intercepted in the homepage and redirected to `/rankkernel/`. Three different
titles are used for RankKernel across the data file, which is drift.

---

## 6. SKILLS ECOSYSTEM (7 categories, 41 skills)

`SYS.01` WordPress (6 skills) · `SYS.02` PHP (6) · `SYS.03` APIs (6) ·
`SYS.04` Databases (5) · `SYS.05` Security (6) · `SYS.06` Integrations (6) ·
`SYS.07` Tools (5)

Each skill is `{ name, context }` where context is a one-sentence explanation of
**how** the skill is used, not a claim of level. For example Security contains:

- `HMAC SHA 256`: Cryptographic signature generation and verification for cross domain REST payloads
- `Nonce Verification`: Protecting form submissions and AJAX actions against cross site request forgery
- `Output Escaping`: Context specific escaping before rendering dynamic data into HTML or attributes
- `Capability Checks`: Role based authorization restricting sensitive views and decryption operations

**This is exactly the pattern your no-percentage rule calls for.** Each skill
explains its concrete use rather than asserting a level. It should be preserved
verbatim.

**No numeric weight or importance value exists on any skill.** The only numbers
in the skills area are geometric orbit values on the hero nodes.

Each category also has 2 `connectedProjects`, cross-linking the ecosystem to
real case studies.

### 6.1 Hero orbit nodes (8)

WordPress, PHP, Plugin, API, Database, Security, Git, RankKernel. Each has
`orbitRadius`, `orbitAngle`, `elevation` (pure geometry, not skill levels), a
summary, and a `metrics` string that is a middot-joined list of skill names.

**Note the field is named `metrics` but contains no numbers.** It is a list of
skill names. Naming is misleading; rename to `skillLabels` on the way in, or a
future reader will assume there are numbers to strip.

---

## 7. RANKKERNEL (9 subsystems, honest status)

Status tally: **9 COMPLETED, 0 RUNNING, 5 PLANNED.** The registry reserves 14
module ids. Nine are shipped. Five are deliberate reservations with no
implementation. The project document never describes a module as partially
delivered, so RUNNING is zero and not a placeholder.

| Status | Subsystems |
|---|---|
| COMPLETED | Metadata Engine, Content Analysis, XML Sitemaps, Schema and JSON-LD, Breadcrumbs, Robots.txt and llms.txt, Redirects, 404 Monitor, Instant Indexing |
| RUNNING | None. No module is recorded as mid-build |
| PLANNED | Importer, Image SEO, Gutenberg Suite, AI Suite, Headless. Reserved ids with nothing on disk |

PLANNED is not a build promise here. The document calls these five deliberate
reservations and the code is honest about that rather than shipping a stub.

Five of the nine shipped modules are on by default and four are off by default,
which is what keeps a default install lean. All nine are COMPLETED, because the
status records delivery, not the default enable flag.

This matches the verified capabilities exactly. Nothing is overstated. The
`independenceNotice` is explicit:

> RankKernel is an independent personal open source software project built by
> Maulik Bhalodiya. It is not affiliated with Qrolic Technologies and is not
> client work.

`connectedNodes` is a real undirected graph with 14 edges. That is a genuine
dependency graph, not decoration, and it is the strongest asset on the RankKernel
page.

---

## 8. EXPERIENCE (2 entries, NO DATES)

| Role | Company | Period string |
|---|---|---|
| WordPress Developer | Qrolic Technologies | `Current Professional Role` |
| PHP Developer Intern | Qrolic Technologies | `Prior Internship Role` |

**There are no calendar dates anywhere in the design.** The period fields hold
the strings "Current Professional Role" and "Prior Internship Role".

Earlier research established the verified timeline from the resume as: PHP
Developer Intern Jan to Jun 2025, WordPress Developer full time Jul 2025 to
present, roughly 2 years. The design omits all of it. Adding those dates is
verifiable content, not invention, and a recruiter-facing timeline without dates
is weak.

**Company spelling is consistent: `Qrolic Technologies` throughout.** The
resume's "Qrologic" is the only wrong variant and it is not in the design. Good.

**Education** is in `PROFILE_DATA`, not `EXPERIENCE_DATA`: B.Tech in Computer
Engineering, Ganpat University, `2021 to 2025`.

---

## 9. ENGINEERING APPROACH (5 stages)

Understand, Design, Build, Verify, Improve. Each has a headline, description,
3 engineering questions and 3 artifacts.

**This is a strong section and it is fully verified content.** The questions are
the kind of thing that demonstrates seniority without claiming anything
unverifiable. Example from stage 04 Verify:

> 'Can an unprivileged user bypass the UI and call the AJAX or REST endpoint directly?'

That single question signals more competence than any metric could.

---

## 10. THE DATA FILE'S OWN WORDPRESS PLAN, AND WHY IT CONFLICTS

`portfolioData.ts` ends with a `WORDPRESS_THEME_BLUEPRINT` object proposing:

- A `project` CPT with taxonomies `project_category`, `project_technology`,
  `work_classification`
- A `post` CPT for future technical articles, taxonomies `category`/`post_tag`
- 4 custom dynamic blocks: `maulik/project-card`,
  `maulik/project-architecture`, `maulik/rankkernel-status`,
  `maulik/related-projects`
- theme.json tokens: contentSize 1200px, wideSize 1440px

**This directly contradicts two decisions already made:**

1. **Research established that custom post types and custom blocks that read
   project data are forbidden in a wp.org directory theme.** The directory's own
   wording is "Shortcodes, custom post types, and custom blocks are not allowed
   in themes", and its dividing line is functionality that is not design and
   presentation. All four blocks named above fall on the plugin side of that line
   because each reads project data. The blueprint is a plugin architecture.
2. **The decision was Path B, everything inside the theme, no submission.**

There is a third problem: the blueprint's own field list **omits
`architectureFlows`**, which the architecture block renders. It would break on
first use.

**The blueprint's instinct is right but its mechanism is wrong.** Structured
content separated from presentation is correct. The WordPress-native way to get
that inside a theme-only constraint is **block attributes in post content**, not
a CPT. Project data becomes nested blocks. The content model survives, the
plugin territory does not.

The `post` future-articles entry is worth keeping as an intent note, since blog
support is a stated requirement.

---

## 11. DASH COMPLIANCE: CLEAN

A regex sweep for `U+2014` (em), `U+2013` (en), `U+2012` (figure), `U+2015`
(horizontal bar) across the whole repo returned **exactly one hit**, and it is
not content:

`vite.config.ts` line 16, a developer comment.

**All site content uses `·` (U+00B7) as the separator and plain ASCII hyphens.**
The design is fully compliant with the no-dashes rule as it stands.

One style note to normalise: prose says "cross domain" unhyphenated, but slugs
and node ids use "cross-domain". Both forms appear. Pick one for prose.

---

## 12. SUMMARY OF WHAT MUST BE DECIDED

1. **Project 05 Rank Math.** Ship the placeholder page, reduce it to a verified
   summary, or remove it. Recommendation: reduce.
2. **The archive filter vocabulary.** Five real defects. Reconcile or accept.
3. **RankKernel page scope.** The design has a full 555-line page and a nav
   item. Earlier instruction was portfolio first, RankKernel later. They
   disagree.
4. **Timeline dates.** The design has none. Verified dates exist and should be
   added.
5. **The CPT conflict.** The design's own blueprint proposes a CPT and 4 custom
   blocks, which is forbidden in a theme. Confirm block attributes instead.
6. **Rejected options.** Seven of nine decisions have no alternative named.
   Adding it is a real content task, not a port.
7. **The `metrics` field name** on hero nodes contains no numbers. Rename on the
   way in to prevent future confusion.
