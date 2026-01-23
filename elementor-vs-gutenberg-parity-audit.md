# Elementor vs Gutenberg Parity Audit

Elementor is the reference. This audit compares Elementor vs Gutenberg for **all styles** and documents output parity, editor parity, and attribute persistence. It ends with a prioritized fix plan and acceptance tests.

## Sources Reviewed
- Elementor widget: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\includes\frontend\elementor\class-grp-elementor-widget.php`
- Elementor CSS: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\assets\css\elementor.css`
- Shortcode renderer (shared output): `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\includes\class-grp-shortcode.php`
- Gutenberg block JS: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\assets\js\gutenberg-block.js`
- Gutenberg PHP render: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\includes\frontend\class-grp-gutenberg.php`
- Frontend CSS: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\assets\css\frontend.css`
- Gutenberg CSS: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\assets\css\gutenberg-block.css`
- Gutenberg editor CSS: `c:\Users\User\Local Sites\wooalisync\app\public\wp-content\plugins\GooRev\assets\css\gutenberg-block-editor.css`

---

## Global Output Parity (All Styles)

### A) Output parity (Frontend DOM/CSS)
**Elementor DOM (carousel)**  
Elementor output uses the shared shortcode renderer (same HTML) but Elementor’s widget CSS targets `.elementor-widget-grp-reviews`. The shortcode renderer currently outputs:
- `.grp-reviews` wrapper with classes: `grp-style-{style}`, `grp-theme-{theme}`, `grp-layout-{layout}`
- Carousel structure: `.grp-carousel-frame` → `.grp-carousel-viewport` → `.grp-carousel-track`
- Dots container: `.grp-carousel-dots`

**Elementor CSS mismatch:**  
`elementor.css` still references `.grp-carousel-container` and `.grp-carousel-wrapper`, while the shared shortcode outputs `.grp-carousel-frame`, `.grp-carousel-viewport`, `.grp-carousel-track`. This is a **structural mismatch** in the Elementor stylesheet and causes ambiguity about which CSS is authoritative.

**Gutenberg DOM (carousel)**  
Gutenberg uses ServerSideRender and the same shared shortcode HTML. Editor preview wraps output inside `.grp-gutenberg-block-wrapper` and `.grp-gutenberg-block-editor`.

**Wrapper classes parity**
- Elementor: `.elementor-widget-grp-reviews`
- Gutenberg: `.grp-gutenberg-block` and `.grp-gutenberg-block-wrapper`
These wrappers are **not equivalent** and affect selector targeting.

**CSS variables**
Both sides rely on `--grp-*` variables. Gutenberg sets variables in JS and PHP, Elementor uses Elementor selectors to apply styles to elements.

### B) Editor parity (Controls & behaviour)
**Elementor controls (high-level)**
- Content: style, theme, layout, columns (desktop/tablet/mobile), gap, count, rating filters, sort.
- Display: show avatar/date/rating/reply, consistent height.
- Carousel: autoplay, speed, dots, arrows.
- Arrow styling: icon/background colors, hover background, icon size, button size, border radius, horizontal/vertical position, box shadow, icon selection.
- Dot styling: color, active color, size, spacing, border radius.
- Style customization: text color, background, border color, accent color, border radius, padding, font sizes, avatar size, text alignment, box shadow.
- Creative style: gradient, glass effect, creative colors, avatar size, star size.

**Gutenberg controls (high-level)**
- Content: style, theme, layout, columns (desktop), (tablet/mobile for Pro), gap, count, rating filters, sort.
- Display: show avatar/date/rating/reply, consistent height.
- Carousel: autoplay, speed, dots, arrows.
- Arrow styling: size, icon size, icon color, background/hover background, border radius, X/Y positions.
- Dot styling: color, active color, size, spacing, border radius.
- Style customization: text/background/border/accent/star colors, font sizes.
- Creative style: gradient, glass effect, creative colors, avatar size, star size.

**Missing or reduced in Gutenberg vs Elementor**
- No controls for card padding, border radius, box shadow, avatar size, text alignment.
- No arrow icon selection or arrow box shadow control.
- No per-style overrides for text alignment like Elementor.
- Elementor uses selector mapping; Gutenberg relies on CSS variables and requires consistent CSS consumption.

### C) Attribute + persistence check (global)
**Expected flow:** Gutenberg attributes → ServerSideRender → PHP render callback → Shortcode → inline CSS vars → CSS consumes variables.
**Risk:** If any attribute is missing from schema, fails validation, or not consumed by CSS selectors, preview and frontend diverge.

---

## Style-by-style Audit

### 1) Modern
#### A) Output parity (Frontend DOM/CSS)
- DOM structure is identical via shared shortcode.
- Elementor stylesheet targets `.elementor-widget-grp-reviews` selectors, Gutenberg uses `.grp-gutenberg-block` and `.grp-gutenberg-block-wrapper`.
- `elementor.css` carousel selectors reference `.grp-carousel-container`/`.grp-carousel-wrapper`; shortcode outputs `.grp-carousel-frame`/`.grp-carousel-viewport`/`.grp-carousel-track`.

#### B) Editor parity (Controls & behaviour)
- Elementor: full style controls (padding, border radius, box shadow, text alignment, avatar size).
- Gutenberg: only color + font size controls.
- **Hypothesis:** Missing controls + CSS selectors in Gutenberg account for missing preview updates.

#### C) Attribute + persistence check
- Text/bg/border/accent/star/font sizes: attributes exist in JS and PHP.
- Gutenberg preview uses CSS vars in editor CSS.
- Frontend uses CSS vars in `frontend.css`.
- **Hypothesis:** SSR failures or attribute validation issues stop preview updates and frontend persistence.

---

### 2) Classic
#### A) Output parity (Frontend DOM/CSS)
- DOM structure is shared.
- Classic-specific CSS is in `frontend.css` under `.grp-style-classic`.
- Gutenberg applies global CSS with `.grp-gutenberg-block` wrapper; Elementor uses `.elementor-widget-grp-reviews`.

#### B) Editor parity (Controls & behaviour)
- Elementor has Classic border controls, padding, and alignment.
- Gutenberg exposes only color + font sizes.

#### C) Attribute + persistence check
- Border color should map to `--grp-border-color`.
- **Hypothesis:** missing or non-applied selector in Gutenberg preview for Classic-specific variants; check `.grp-style-classic` blocks in editor CSS.

---

### 3) Minimal
#### A) Output parity (Frontend DOM/CSS)
- Shared DOM.
- Minimal-specific layout styles in `frontend.css` under `.grp-style-minimal`.
- Elementor uses widget-scoped selectors.

#### B) Editor parity (Controls & behaviour)
- Elementor has padding, alignment, avatar sizing, and box shadow adjustments.
- Gutenberg lacks those controls and relies on global CSS.

#### C) Attribute + persistence check
- Same as Classic; CSS vars present but may not be consumed for Minimal-specific overrides in editor CSS.

---

### 4) Corporate
#### A) Output parity (Frontend DOM/CSS)
- Shared DOM and review meta structure. Corporate-specific meta layout in `frontend.css`.
- Elementor CSS includes `.elementor-widget-grp-reviews` scoping; Gutenberg uses `.grp-gutenberg-block` scoping.
- Known issue: avatar/name inline alignment must match Corporate rules; Gutenberg CSS has additional overrides.

#### B) Editor parity (Controls & behaviour)
- Elementor: full style customization controls (padding, border radius, alignment, avatar size).
- Gutenberg: limited to color + font sizes.

#### C) Attribute + persistence check
- Border/accent variables are consumed in editor CSS.
- **Hypothesis:** SSR attribute schema mismatch or missing CSS consumption causes preview/frontend mismatch.

---

### 5) Creative
#### A) Output parity (Frontend DOM/CSS)
- Shared DOM, but creative gradient and glass effects differ in handling:
  - Elementor uses object-type gradient controls; Gutenberg uses split attributes (`creative_gradient_*`).
- Gutenberg removes object attributes from REST to avoid validation errors.

#### B) Editor parity (Controls & behaviour)
- Elementor: full creative gradient + glass effects, creative sizes, creative colors.
- Gutenberg: gradient type/angle/colors, glass effect, avatar/star sizes.
- **Hypothesis:** gradient and glass effects may not match because Elementor applies per-widget selectors whereas Gutenberg relies on inline `custom_css` with CSS variables.

#### C) Attribute + persistence check
- Creative attributes are defined in JS and PHP.
- Custom CSS is injected in PHP for creative styles.
- **Risk:** missing object attributes or mismatched control names (Elementor object vs Gutenberg scalar fields).

---

## Attribute Flow Summary (Key Style Controls)

**Color controls**
- `custom_text_color`, `custom_background_color`, `custom_border_color`, `custom_accent_color`, `custom_star_color`
  - JS: defined and used in wrapper styles.
  - PHP: defined in schema, passed to shortcode.
  - CSS: consumed in `gutenberg-block.css`, `gutenberg-block-editor.css`, and `frontend.css`.
  - **Potential mismatch:** if SSR fails, preview does not update.

**Typography controls**
- `custom_font_size`, `custom_name_font_size`
  - Same flow as above.
  - **Gap:** Elementor also controls font family, text alignment, padding, box shadow, border radius.

**Arrow controls**
- `arrow_size`, `arrow_icon_size`, `arrow_icon_color`, `arrow_background_color`, `arrow_hover_background_color`, `arrow_border_radius`, `arrow_horizontal_position`, `arrow_vertical_position`
  - JS: defined and used in wrapper styles; controls present.
  - PHP: defined in schema and passed to shortcode.
  - CSS: consumed in `gutenberg-block.css` and `frontend.css`.
  - **Gap:** Elementor has arrow icon selector and box shadow controls; Gutenberg lacks.

**Dot controls**
- `dot_color`, `dot_active_color`, `dot_size`, `dot_spacing`, `dot_border_radius`
  - JS: defined and controls exist.
  - PHP: defined in schema and passed to shortcode.
  - CSS: consumed in `gutenberg-block.css` and `frontend.css`.
  - **Gap:** Ensure dots JS generation respects layout and uses CSS variables.

---

## Discrepancy Checklist (Top Issues)
1. **Carousel DOM mismatch in Elementor CSS**: `elementor.css` references `.grp-carousel-container`/`.grp-carousel-wrapper`, but shared shortcode outputs `.grp-carousel-frame`/`.grp-carousel-viewport`/`.grp-carousel-track`.
2. **Selector scoping mismatch**: Elementor uses `.elementor-widget-grp-reviews` while Gutenberg uses `.grp-gutenberg-block` wrappers. CSS selectors are not aligned across contexts.
3. **Missing Gutenberg controls**: padding, border radius, avatar size, text alignment, box shadow, arrow icon selection, arrow box shadow.
4. **Creative style object mismatch**: Elementor uses object-based gradient controls; Gutenberg uses scalar attributes, with object fields removed from REST to avoid 400s.

---

## Post-latest update status

### Carousel horizontal cropping (preview)
- **Status:** Preview
- **Expected (Elementor):** 1–2 columns only show configured number of cards; overflow clipped within frame.
- **Actual (Gutenberg):** 1–2 columns still show extra cards outside the frame.
- **Root cause hypothesis:** Editor CSS not fully enforcing viewport clipping; preview wrapper styles may not mirror frontend frame/viewport contract.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `.grp-carousel-frame`, `.grp-carousel-viewport`, `.grp-carousel-track`
- **Fix approach:** Ensure editor uses frame → viewport (overflow hidden) → track; enforce overflow hidden with padding for shadows.
- **Acceptance test:** In editor preview, 1–2 columns show only visible cards inside frame; no overflow.

### Card background color not applying
- **Status:** Both
- **Expected (Elementor):** Card background updates in preview and frontend when control changes.
- **Actual (Gutenberg):** Preview stays white; frontend unverified but treat as broken until confirmed.
- **Root cause hypothesis:** Editor theme styles hardcode background and override CSS vars; theme styles on frontend override custom vars.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `assets/css/frontend.css`, `.grp-review`
- **Fix approach:** Use CSS variables for background in editor + frontend and ensure theme styles set defaults via vars.
- **Acceptance test:** Card background changes are visible in preview and on frontend without saving.

### Dots preview + dot sizing not working
- **Status:** Both
- **Expected (Elementor):** Dots visible in preview; size/spacing/radius apply in preview + frontend.
- **Actual (Gutenberg):** Dots missing in preview; dot sizing controls do not apply reliably.
- **Root cause hypothesis:** Dots are JS-generated on frontend only; editor lacks dot markup and CSS variable consumption.
- **Files/classes involved:** `includes/class-grp-shortcode.php`, `assets/css/gutenberg-block-editor.css`, `.grp-carousel-dots`, `.grp-dot`
- **Fix approach:** Render dots server-side for preview; add editor CSS for dots using vars.
- **Acceptance test:** Dots visible in preview; size/spacing/radius update in preview + frontend.

### Arrows preview not applying
- **Status:** Preview
- **Expected (Elementor):** Arrow icon/color/radius/position update live in editor.
- **Actual (Gutenberg):** Arrow styles only reflect on frontend; preview stays static.
- **Root cause hypothesis:** Editor CSS lacks arrow styling and variable consumption.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `.grp-carousel-prev`, `.grp-carousel-next`
- **Fix approach:** Mirror frontend arrow CSS in editor and consume CSS vars from wrapper.
- **Acceptance test:** Arrow styling changes apply immediately in preview.

### Text alignment controls not working / incomplete
- **Status:** Both
- **Expected (Elementor):** Text alignment applies consistently to review text + meta + name.
- **Actual (Gutenberg):** Alignment control does not affect layout; meta/name remain left-aligned.
- **Root cause hypothesis:** Editor CSS forces left alignment; frontend alignment uses class-based overrides not vars.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `assets/css/frontend.css`, `.grp-review`, `.grp-review-meta`, `.grp-review-author`
- **Fix approach:** Apply alignment via CSS variables at wrapper; remove hardcoded left alignment.
- **Acceptance test:** Center alignment centers review text + meta + name in preview + frontend.

### Box shadow control UX + correctness
- **Status:** Both
- **Expected (Elementor):** Shadow control updates card shadow in preview + frontend.
- **Actual (Gutenberg):** Shadow input unclear; results inconsistent in preview.
- **Root cause hypothesis:** Shadow value not mapped to CSS vars in editor; theme/default shadow overrides.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `assets/css/frontend.css`, `.grp-review`
- **Fix approach:** Map shadow input to `--grp-card-shadow` and consume in editor + frontend.
- **Acceptance test:** Card shadow changes are visible in preview + frontend.

### Light/Dark theme toggle not working
- **Status:** Preview
- **Expected (Elementor):** Theme toggle changes preview and frontend output.
- **Actual (Gutenberg):** Theme toggle does not change review preview.
- **Root cause hypothesis:** Editor theme styles not applied or overridden; theme values not represented via vars.
- **Files/classes involved:** `assets/css/gutenberg-block-editor.css`, `.grp-theme-dark`, `.grp-theme-light`
- **Fix approach:** Add theme variables in editor CSS and ensure review styles use vars.
- **Acceptance test:** Theme toggle changes preview and frontend styles.

### Avatar size controls not applying
- **Status:** Both
- **Expected (Elementor):** Avatar size updates in preview + frontend.
- **Actual (Gutenberg):** Avatar size does not reflect reliably in preview/frontend.
- **Root cause hypothesis:** Style-specific avatar rules override size; editor lacks var-based sizing.
- **Files/classes involved:** `assets/css/frontend.css`, `assets/css/gutenberg-block-editor.css`, `.grp-review-avatar img`
- **Fix approach:** Replace hardcoded avatar sizes with CSS vars and update style-specific defaults.
- **Acceptance test:** Avatar size changes apply in preview + frontend for all styles.

---

## Fix Plan (Prioritized)

### Priority 0 — Blockers (SSR + attribute validation)
**Goal:** SSR endpoint returns 200 and preview renders.
- Files: `assets/js/gutenberg-block.js`, `includes/frontend/class-grp-gutenberg.php`
- Attributes: all block attributes listed above.
- DoD: `/wp-json/wp/v2/block-renderer/google-reviews/reviews` returns HTML with full attribute set; Gutenberg preview loads without 400.

### Priority 1 — Layout parity (carousel clipping)
**Goal:** no cards overflow outside frame for 1/2/3+ columns.
- Files: `includes/class-grp-shortcode.php`, `assets/css/frontend.css`, `assets/css/gutenberg-block.css`, `assets/css/gutenberg-block-editor.css`
- Selectors: `.grp-carousel-frame`, `.grp-carousel-viewport`, `.grp-carousel-track`
- DoD: Only configured number of cards visible; overflow hidden in viewport; box shadows preserved.

### Priority 2 — Style persistence (preview + frontend)
**Goal:** All style controls apply in preview and frontend.
- Files: `assets/js/gutenberg-block.js`, `includes/frontend/class-grp-gutenberg.php`, `includes/class-grp-shortcode.php`, `assets/css/gutenberg-block.css`, `assets/css/gutenberg-block-editor.css`, `assets/css/frontend.css`
- Attributes: `custom_*`, `arrow_*`, `dot_*`, `custom_font_size`, `custom_name_font_size`.
- DoD: Changing any control updates preview immediately; saved post shows same styling.

### Priority 3 — Carousel UI parity (arrows/dots)
**Goal:** Arrow/dot controls match Elementor.
- Files: `assets/js/gutenberg-block.js`, `assets/css/gutenberg-block.css`, `assets/css/frontend.css`
- Attributes: `arrow_*`, `dot_*`
- DoD: arrows/dots visually match Elementor; positioning and styles respond to control changes.

### Priority 4 — Template parity by style
**Goal:** each style aligns with Elementor.
- Files: `assets/css/frontend.css`, `assets/css/gutenberg-block.css`, `assets/css/gutenberg-block-editor.css`
- Styles: modern, classic, minimal, corporate, creative
- DoD: markup classes, CSS selectors, and variable usage match Elementor output.

---

## Acceptance Tests
1. Gutenberg editor preview renders without SSR errors.
2. All color controls (text/bg/border/accent/star) update preview immediately.
3. Arrow and dot styling updates preview immediately.
4. Save post → all styles persist on frontend.
5. Carousel clipping works for 1, 2, 3+ columns with shadows preserved.
6. Gutenberg output matches Elementor output for all styles.
