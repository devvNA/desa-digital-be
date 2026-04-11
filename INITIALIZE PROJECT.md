# Step 1: Analyze Codebase

As a technical expert, analyze the codebase and cover:

- **Core Components**: Tech stack, major modules/classes, responsibilities, design patterns
- **Component Interactions**: Data/control flow, APIs, interfaces, DI/service patterns
- **Deployment Architecture** _(if applicable)_: Build steps, environments, infra/containerization
- **Runtime Behavior**: Initialization, request handling, error management, background tasks

Stay factual — no opinions or suggestions unless asked.

# Step 2: Generate `TECHNICAL_OVERVIEW.md`

Save Step 1 output as `TECHNICAL_OVERVIEW.md` in the project root.

# Step 3: Generate `AGENTS.md`

Using skill `agents-md-generator`, create `AGENTS.md` as a README for AI coding agents. Follow these rules:

- Wrap commands in backticks for copy-paste clarity
- Link to existing docs — never duplicate them
- Keep instructions precise and verifiable
- Require objective proof of completion: tests, lint, type check, diff scoped to agreed paths

# Step 4: Generate `DESIGN_SYSTEM.md`

Scan the codebase for existing UI patterns and save as `DESIGN_SYSTEM.md`. Cover:

- **Colors & Typography**: tokens/variables found (hex, rem, font families)
- **Component Library**: reusable UI components, props/variants, file locations
- **Spacing & Layout**: grid system, breakpoints, spacing scale
- **Patterns & Conventions**: naming conventions, theming approach (CSS vars, Tailwind config, theme files)

Rules:

- Reference actual file paths, not assumptions
- If no design system exists, document the _de facto_ patterns found in the code
- Use tables where possible — keep entries brief
- Skip sections with zero findings
