# Vue 3 Script Setup Rules

**File Pattern**: `\.(vue)$`

## Component Structure

### Non-Script Setup Usage
- **Pattern**: `<script>(?!.*setup)`
- **Message**: Consider using script setup syntax for simpler and more concise components
- **Severity**: suggestion

### Mixing Options and Setup
- **Pattern**: `<script setup>.*export default`
- **Message**: Avoid mixing script setup with options API. Use one approach consistently
- **Severity**: warning

### Complex Component Logic
- **Pattern**: `<script setup>(?:.|\n){500,}</script>`
- **Message**: This component might be too complex. Consider breaking it down into smaller components
- **Severity**: suggestion

### Too Many Refs
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*(?:ref\()[^)]*\)(?:[^{}]|{[^{}]*})*(?:ref\()[^)]*\)(?:[^{}]|{[^{}]*})*(?:ref\()[^)]*\)(?:[^{}]|{[^{}]*})*(?:ref\()[^)]*\)(?:[^{}]|{[^{}]*})*(?:ref\()[^)]*\)`
- **Message**: Too many refs in one component. Consider grouping related state or using reactive
- **Severity**: suggestion

### Export Default Usage
- **Pattern**: `<script setup>[\s\S]*export default[\s\S]*</script>`
- **Message**: Avoid using 'export default' with script setup. Use defineProps, defineEmits, and other script setup utilities instead
- **Severity**: error

### Legacy Options API
- **Pattern**: `export default\s*{`
- **Message**: Don't use Options API (export default). Use <script setup> syntax exclusively for consistency
- **Severity**: error

### Regular Script Tag
- **Pattern**: `<script>(?!.*setup)`
- **Message**: Use <script setup> instead of regular <script> for all components
- **Severity**: error

### Mixing Script Types
- **Pattern**: `<script>[\s\S]*</script>\s*<script setup>`
- **Message**: Don't use both regular <script> and <script setup> in the same component
- **Severity**: error

## Reactivity

### Raw Object Mutation
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*(?:ref|reactive)(?:[^{}]|{[^{}]*})*\.\w+\s*=`
- **Message**: Directly mutating reactive objects can cause issues. Use destructuring or computed for derived values
- **Severity**: warning

### Ref Value Access
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*(\w+)\s*=\s*ref\([^)]*\)(?:[^{}]|{[^{}]*})*\1\s*=`
- **Message**: Use .value when modifying ref values outside of the template: refName.value = newValue
- **Severity**: error

### Unused Imports
- **Pattern**: `import\s+{\s*([^{}]*?)\s*}\s+from\s+['"]vue['"](?![\s\S]*\1)`
- **Message**: Imported Vue APIs are unused. Remove unused imports for better performance
- **Severity**: warning

### Avoid Props Mutation
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*defineProps\((?:[^{}]|{[^{}]*})*\)(?:[^{}]|{[^{}]*})*props\.\w+\s*=`
- **Message**: Avoid mutating props directly. Create local state based on props instead
- **Severity**: error

## Composition Functions

### Missing Return Value
- **Pattern**: `function use[A-Z]\w*\([^)]*\)\s*{(?![^{}]*return\s+{)`
- **Message**: Composition functions should return an object with values and functions
- **Severity**: warning

### Non-prefixed Composition Functions
- **Pattern**: `function (?!use)[a-z]\w*\([^)]*\)\s*{(?:[^{}]|{[^{}]*})*(?:ref|reactive|computed|watch)`
- **Message**: Prefix composition functions with 'use' for better code organization (e.g., 'useFeature')
- **Severity**: suggestion

## Template Structure

### Long Template Lines
- **Pattern**: `<template>(?:[^<]|<(?!template))*(.{120,})(?:[^>]|>(?!\/template))*<\/template>`
- **Message**: Template lines are too long. Break up complex expressions for readability
- **Severity**: suggestion

### v-if with v-for
- **Pattern**: `<\w+[^>]*v-for[^>]*v-if[^>]*>`
- **Message**: Avoid using v-if and v-for on the same element. Use a wrapper element or computed property
- **Severity**: warning

### Missing Key in v-for
- **Pattern**: `<\w+[^>]*v-for[^>]*(?!:key|v-bind:key)[^>]*>`
- **Message**: Always use a key with v-for directives for better performance and predictable behavior
- **Severity**: warning

### Computed Property in Template
- **Pattern**: `{{.*\s+\?\s+.*\s+:\s+.*}}`
- **Message**: Complex conditional logic in templates should be moved to computed properties
- **Severity**: suggestion

## Lifecycle and Side Effects

### Side Effects in Setup
- **Pattern**: `<script setup>(?!(?:[^{}]|{[^{}]*})*onMounted)(?:[^{}]|{[^{}]*})*(?:fetch|axios|localStorage|setTimeout|setInterval)`
- **Message**: Side effects should be wrapped in lifecycle hooks like onMounted or onBeforeMount
- **Severity**: warning

### Missing Cleanup
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*(?:addEventListener|setInterval|setTimeout)(?![^{}]*onUnmounted)`
- **Message**: Clean up event listeners, intervals, or timeouts in onUnmounted to prevent memory leaks
- **Severity**: warning

## Event Handling

### Inline Function Handlers
- **Pattern**: `@[a-z]+="(?!\$event|\w+\()\([^)]*\)\s*=>"`
- **Message**: Avoid inline arrow functions in event handlers. Use methods instead for better performance
- **Severity**: suggestion

### Complex Event Handlers
- **Pattern**: `@[a-z]+="[^"]{50,}"`
- **Message**: Event handler logic is complex. Move it to a separate function for better readability
- **Severity**: suggestion

## Props and Emits

### Missing Props Definition
- **Pattern**: `<script setup>(?![^{}]*defineProps)`
- **Message**: Consider using defineProps to document the component's API even if no props used yet
- **Severity**: suggestion

### Missing Emits Definition
- **Pattern**: `<script setup>(?:[^{}]|{[^{}]*})*emit\((?![^{}]*defineEmits)`
- **Message**: Use defineEmits to document events that the component can emit
- **Severity**: warning

### Non-Validated Props
- **Pattern**: `defineProps\(\[`
- **Message**: Use object syntax with validation instead of array syntax for defineProps
- **Severity**: suggestion

## Styles

### Scoped vs. Global Styles
- **Pattern**: `<style(?!.*scoped)>`
- **Message**: Consider using scoped styles to prevent global CSS conflicts
- **Severity**: suggestion

### Complex Selectors
- **Pattern**: `<style[^>]*>(?:[^{}]|{[^{}]*})*([^{]*>[^{]*>[^{]*>[^{]*{)`
- **Message**: Overly complex CSS selectors. Simplify or use component composition
- **Severity**: suggestion

### BEM Notation Consistency
- **Pattern**: `<style[^>]*>(?:[^{}]|{[^{}]*})*\.-[a-z]+-[a-z]+`
- **Message**: Consider using consistent BEM notation for CSS class names
- **Severity**: suggestion

## Performance

### Heavy Computed Properties
- **Pattern**: `const\s+\w+\s*=\s*computed\(\(\)\s*=>\s*{(?:[^{}]|{[^{}]*}){50,}}\)`
- **Message**: Computed property is complex. Consider breaking it down or using a cached value
- **Severity**: suggestion

### Multiple Watchers for Same Property
- **Pattern**: `watch\((?:[^{}]|{[^{}]*})*\)(?:[^{}]|{[^{}]*})*watch\(\s*(?:[^{}]|{[^{}]*})*\1`
- **Message**: Multiple watchers on the same property may cause performance issues
- **Severity**: warning
