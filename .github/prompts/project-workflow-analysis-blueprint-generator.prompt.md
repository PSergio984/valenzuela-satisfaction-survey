---
description: 'Comprehensive technology-agnostic prompt generator for documenting end-to-end application workflows. Automatically detects project architecture patterns, technology stacks, and data flow patterns to generate detailed implementation blueprints covering entry points, service layers, data access, error handling, and testing approaches across multiple technologies including .NET, Java/Spring, React, and microservices architectures.'
agent: 'agent'
---

# Project Workflow Analysis Blueprint Generator

## Configuration Variables

${PROJECT_TYPE="Auto-detect|.NET|Java|Spring|React|Angular|Node.js|Python|Microservices|Other"} <!-- Primary technology -->
${INCLUDE_ARCHITECTURE=true|false} <!-- Include architecture diagrams -->
${INCLUDE_DATA_FLOW=true|false} <!-- Include data flow diagrams -->
${INCLUDE_ERROR_HANDLING=true|false} <!-- Document error handling patterns -->
${INCLUDE_TESTING=true|false} <!-- Document testing approaches -->
${OUTPUT_FORMAT="Markdown|JSON|YAML|HTML"} <!-- Select output format -->

## Generated Prompt

"Analyze the codebase and generate a comprehensive workflow blueprint that documents the end-to-end application workflow. Use the following approach:

### 1. Entry Points

- Identify all application entry points (APIs, UI, CLI, etc.)
- Document how requests are received and routed

### 2. Service Layer

- Identify core services and their responsibilities
- Document service orchestration and business logic flow
- Show how services interact with each other

### 3. Data Access

- Document data access patterns (ORM, direct SQL, API calls)
- Show how data flows from services to storage and back
- Include caching and data transformation layers

### 4. Error Handling

${INCLUDE_ERROR_HANDLING ? "- Document error handling strategies at each layer\n- Show how errors are propagated and logged\n- Include retry and fallback mechanisms" : ""}

### 5. Testing

${INCLUDE_TESTING ? "- Document testing strategies for each layer\n- Include unit, integration, and end-to-end testing approaches\n- Show how test data is managed and validated" : ""}

### 6. Architecture & Data Flow

${INCLUDE_ARCHITECTURE ? "- Generate architecture diagrams showing component relationships\n- Show technology stack and deployment topology" : ""}
${INCLUDE_DATA_FLOW ? "- Generate data flow diagrams showing how data moves through the system" : ""}

### 7. Output

- Format the output as ${OUTPUT_FORMAT}
- Save the output as 'Project_Workflow_Analysis_Blueprint.${OUTPUT_FORMAT == "Markdown" ? "md" : OUTPUT_FORMAT.toLowerCase()}'
  "
