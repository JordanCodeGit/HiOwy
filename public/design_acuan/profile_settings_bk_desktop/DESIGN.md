---
name: Calm Haven
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#45464d'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#006398'
  on-secondary: '#ffffff'
  secondary-container: '#5bb8fe'
  on-secondary-container: '#00476e'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#002114'
  on-tertiary-container: '#069669'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#cce5ff'
  secondary-fixed-dim: '#93ccff'
  on-secondary-fixed: '#001d31'
  on-secondary-fixed-variant: '#004b73'
  tertiary-fixed: '#85f8c4'
  tertiary-fixed-dim: '#68dba9'
  on-tertiary-fixed: '#002114'
  on-tertiary-fixed-variant: '#005137'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  display-lg:
    fontFamily: Montserrat
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Montserrat
    fontSize: 30px
    fontWeight: '700'
    lineHeight: 38px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Montserrat
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  title-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 26px
  title-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 26px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 22px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-sm: 1rem
  gutter-lg: 2rem
  margin: 1.5rem
  margin-sm: 1rem
  margin-lg: 3rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

This design system is tailored for institutional safety, crisis triage, and student reporting platforms. The design philosophy centers on **empathetic clarity**, **unconditional psychological safety**, and **institutional accountability**. The interface must deliberately reject anxiety-inducing visual stimuli—such as abrasive alert reds, harsh black drop shadows, or claustrophobic information density—in favor of a grounded, reassuring environment that lowers cognitive friction for distressed students while offering rigorous operational efficiency for school counselors and administrators.

The visual style blends **Modern Corporate** clarity with **Soft Contemporary Humanism**:
- **Non-punitive Aesthetics**: The visual environment feels confidential, warm, and inviting rather than bureaucratic or disciplinary.
- **Controlled Information Hierarchy**: Interfaces for vulnerable users (students) provide gentle step-by-step guidance with generous breathing room, while administrative dashboards maintain high legibility without sensory overload.
- **Tactile Softness**: Muted tonal surfaces and smooth, deliberate corner radii reinforce a safe digital perimeter, signaling security and attentive institutional care.

## Colors

The palette establishes an immediate atmosphere of stability, emotional neutrality, and protection.

### Primary Palette & Roles
- **Primary Navy / Slate (`#0F172A`, `#1E293B`)**: Represents grounding authority, institutional stability, and rock-solid privacy. Used for high-emphasis typography, critical action buttons, navigation anchors, and dominant structure.
- **Secondary Ocean Blue (`#0284C7`, accent `#0EA5E9`, deep `#0369A1`)**: Symbolizes trust, active empathy, and clarity. Used for primary interactive highlights, progress steppers, active tab states, and conversational accent surfaces.
- **Tertiary Reassuring Sage / Emerald (`#059669`, background tint `#D1FAE5`)**: Conveys safe resolution, verified identity states, and successful submission milestones.
- **Neutral Calm Slates (`#F8FAFC`, `#F1F5F9`, `#E2E8F0`, `#64748B`)**: Provides smooth, low-fatigue structural framing. The default canvas surface sits at `#F8FAFC`, preventing the clinical glare of pure `#FFFFFF` while enabling clean white cards (`#FFFFFF`) to stand out effortlessly.

### Status System
Status indicators rely on paired color-and-tint tokens ensuring WCAG 2.1 AA compliant contrast ratios against their respective light containers:
- **Unread / Newly Logged**: Subtle Indigo (`#6366F1` text/icon on `#EEF2FF` container) indicates fresh triage inputs without inducing panic.
- **Read / In Review**: Calm Sky Blue (`#0284C7` text/icon on `#E0F2FE` container) signals active administrative attention.
- **Awaiting Handling / Follow-up**: Warm Sand Amber (`#D97706` text/icon on `#FEF3C7` container) conveys deliberate operational patience.
- **Resolved / Secured**: Protective Green (`#059669` text/icon on `#D1FAE5` container) delivers a definitive visual feeling of resolution and closure.

## Typography

The typographic system pairs the structured confidence of **Montserrat** with the open, warm geometry of **Plus Jakarta Sans**.

- **Display & Headlines (Montserrat)**: Employed for major page headings, milestone verification values, tracking numbers, and key administrative metric readouts. Its geometric construction communicates purpose and structural strength without appearing intimidating.
- **Body, UI, & Interaction (Plus Jakarta Sans)**: Governs all operational components, multi-step incident forms, guidance microcopy, and conversational narrative bubbles. Its rounded apertures and high x-height guarantee readability for users under emotional stress or those viewing content on smaller mobile devices.
- **Line Length & Ergonomics**: Continuous reading passages and report statements are hard-constrained to a maximum line length of 65 characters (`~680px`), eliminating cognitive drift during emotionally charged writing or review.

## Layout & Spacing

The layout model is built upon a responsive 8pt geometric spacing scale that transitions dynamically across device viewports.

### Breakpoints & Canvas Geometry
- **Mobile (`< 640px`)**: Single-column vertical stream. Screen margins conform strictly to `margin-sm` (`1rem / 16px`). Touch targets adhere to a minimum size of 44×44px, positioned prominently within safe thumb reach.
- **Tablet (`640px – 1024px`)**: Fluid 8-column layout with `gutter-sm` (`1rem`) and `margin` (`1.5rem`). Split-panel view accommodates incident queues alongside quick preview drawers.
- **Desktop (`> 1024px`)**: Fixed structural sidebar (`260px` or `72px` collapsed) anchored beside a 12-column content grid with `gutter` (`1.5rem`) and container constraints maxing out at `1440px`.

### Spacing Principles
- Form progression fields maintain a constant vertical rhythm of `space-md` (`16px`) between complementary fields and `space-xl` (`40px`) between distinct question clusters.
- Conversation and triage logs group context-bound dialogue items tightly using `space-xs` (`4px`) to `space-sm` (`8px`), reserving `space-lg` (`24px`) strictly for changes in conversational actor or temporal milestones.

## Elevation & Depth

This design system deliberately eschews deep, high-opacity, dramatic dropshadows. Depth is achieved via **tonal surface stacking** combined with **whisper-light atmospheric diffusion** and **crisp hairline borders**.

### Layering Philosophy
- **Base Level (`Surface-0`)**: The application foundation sits at `#F8FAFC`.
- **Card & Module Level (`Surface-1`)**: Elevated work surfaces utilize `#FFFFFF` backed by a hairline perimeter border (`1px solid #E2E8F0`). Subtle elevation is provided by an ambient dual-layer shadow: `box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 6px -1px rgba(15, 23, 42, 0.02)`.
- **Raised Interactive Level (`Surface-2`)**: Hovered report items, popovers, and contextual dropdowns step forward with: `box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.05), 0 4px 6px -2px rgba(15, 23, 42, 0.025)`.
- **Modal & Critical Dialogue Level (`Surface-3`)**: Confidentiality agreements, verification prompts, and urgent escalation dialogues use a backdrop veil of `#0F172A` at 40% opacity with `backdrop-filter: blur(4px)`. The surface maintains crisp boundaries without stark drop shadows.

## Shapes

The shape hierarchy establishes an organic, non-threatening geometry that feels approachable to young students while remaining neat and organized for administrators.

- **Status Badges & Indicator Pills**: Fully rounded pill shapes (`border-radius: 9999px`) to distinguish categorical metadata from interactive controls.
- **Input Fields, Dropdowns, & Action Buttons**: Structured at `8px` to `10px` radius, striking a balance between modern geometric discipline and soft, welcoming touch areas.
- **Cards, Panels, & Conversation Containers**: Generously shaped at `12px` to `16px` (`rounded-lg` through `rounded-xl`).
- **Safety Highlight Banners & Confidentiality Blocks**: Outlined containers styled with `12px` inner rounding and an accented `4px` left edge border that commands visual priority without displaying aggressive warning iconography.

## Components

### Buttons
- **Primary**: Solid Deep Navy (`#0F172A`) with pure white text, transitioning to `#1E293B` on hover. Height is 44px on desktop and 48px on mobile with a `10px` border radius. Focus states use an explicit `2px` offset ring in Ocean Blue (`#0284C7`).
- **Empathetic / Secondary Action**: Soft ocean tint (`#E0F2FE`) with deep ocean blue text (`#0369A1`). Conveys safety and low pressure for secondary actions (e.g., "Save Draft", "Provide Additional Details Later").
- **Ghost / Neutral**: Borderless `#64748B` typography, shifting to `#F1F5F9` background on hover. Designed for cancelation or back buttons to prevent visual competition.

### Form Inputs & Textareas
- Built with a `#FFFFFF` fill, a `1px solid #CBD5E1` boundary, and a `10px` border radius. 
- Placeholder text sits at muted slate `#94A3B8`.
- Focus states trigger a crisp transition to `border-[#0284C7]` supported by a soft halo ring: `box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15)`.

### Status Badges & Chips
- Compact, auto-layout horizontal chips with `9999px` border radius, padding `4px 12px`, and `label-sm` weight.
- Never use raw red; use Amber (`#D97706` / `#FEF3C7`) for urgent triage requirements, Sky Blue (`#0284C7` / `#E0F2FE`) for active cases, Purple (`#6366F1` / `#EEF2FF`) for unread items, and Sage (`#059669` / `#D1FAE5`) for resolved inquiries.

### Cards & Category Folders
- Encased in `#FFFFFF`, with `1px solid #E2E8F0` and `16px` corner radius.
- Includes a dedicated header row designed for category classification, timestamping, and an explicit **Identity Protection Switch**:
  - Displays a pill badge specifying `Reporter Identity: Hidden (Anonymous)` in soft slate `#F1F5F9`, or `Reporter Identity: Shared with Counselor` in reassuring sage `#D1FAE5`.

### Conversational Messaging Bubbles
- **Student Reporter Bubble**: Right-aligned, colored with `#0284C7` or deep `#0F172A` with pure white text, with an asymmetric corner radius (`16px 16px 4px 16px`) indicating origin.
- **Counselor / Automated System Bubble**: Left-aligned, light slate surface (`#F1F5F9`), slate-800 text (`#1E293B`), with corner radius (`16px 16px 16px 4px`). Includes an official role verification badge (`Verified Counselor`).

### Tracking Code & Safety Highlight Blocks
- High-priority display block framed in `#FFFFFF` with a thick `4px` accent border on the left in ocean blue (`#0284C7`).
- Prominently showcases the anonymous tracking reference code (e.g., `SAFE-8821-XP`) set in `Montserrat` bold, paired with a copy action button and reassuring microcopy: *"Store this code safely. It is your private, anonymous key to check status updates without revealing who you are."*