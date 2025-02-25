# Laravel 11 Cursor Rules

**File Pattern**: `.php$`

## Strict Types

### Missing Strict Types Declaration
- **Pattern**: `^(?!declare\(strict_types=1\);)`
- **Message**: Add 'declare(strict_types=1);' at the top of the file (PHP 8.1+)
- **Severity**: suggestion

## Models

### Plural Model Names
- **Pattern**: `class ([A-Z][a-z]+)s extends Model`
- **Message**: Models should use singular names (e.g., 'User' not 'Users')
- **Severity**: warning

### Incomplete Model Properties
- **Pattern**: `class [A-Z][a-zA-Z]+ extends Model\s*{\s*(?!.*\$fillable|.*\$guarded)`
- **Message**: Define $fillable or $guarded properties in your models
- **Severity**: warning

### Missing Type Hints in Relationships
- **Pattern**: `public function [a-zA-Z0-9_]+\(\)\s*{\s*return \$this->(?:hasMany|hasOne|belongsTo|belongsToMany)\([^;]*;\s*}`
- **Message**: Add return type hints to model relationships (e.g., 'public function posts(): HasMany')
- **Severity**: warning

## Controllers

### Fat Controllers
- **Pattern**: `class [A-Z][a-zA-Z]+Controller.*?{(?:[^{}]|{[^{}]*})*{[^{}]*}{[^{}]*}{[^{}]*}`
- **Message**: Controllers should be thin. Consider moving logic to services or actions
- **Severity**: suggestion

### Missing Authorization
- **Pattern**: `class [A-Z][a-zA-Z]+Controller.*?{(?![^}]*\$this->authorize)`
- **Message**: Consider using authorization in your controllers
- **Severity**: suggestion

### Missing Request Validation
- **Pattern**: `public function (?:store|update)\([^)]*Request \$request[^)]*\)\s*{(?![^}]*validate\(|[^}]*validated\()`
- **Message**: Validate request data in store/update methods or use Form Request validation
- **Severity**: warning

## Database

### Queries in Loops
- **Pattern**: `foreach[^{]*{[^}]*(?:DB::|Model::).*?}`
- **Message**: Avoid DB queries in loops. Use eager loading, chunking, or collection methods
- **Severity**: warning

### Raw Queries
- **Pattern**: `DB::raw\(`
- **Message**: Prefer query builder or Eloquent over raw SQL when possible
- **Severity**: suggestion

### Schema Table vs Create
- **Pattern**: `Schema::table\([^)]*\)\s*{[^}]*\$table->create`
- **Message**: Use Schema::create() to create new tables, not Schema::table()
- **Severity**: error

### Inappropriate Column Type
- **Pattern**: `\$table->string\('[a-z_]+_id'\)`
- **Message**: For foreign keys, use unsignedBigInteger() or foreignId() instead of string()
- **Severity**: warning

## Routes

### Non-RESTful Route Names
- **Pattern**: `->name\('(?!.*\.)\w+(?:\.\w+){0,1}'\)`
- **Message**: Use resource.action format for route names (e.g., 'users.index')
- **Severity**: suggestion

### Closure Routes
- **Pattern**: `Route::[a-z]+\([^,]*,[\s\n]*function[\s\n]*\([^)]*\)[\s\n]*{`
- **Message**: Avoid closure routes in routes files. Use controller methods instead
- **Severity**: suggestion

## Dependency Injection

### Service Instantiation
- **Pattern**: `new [A-Z][a-zA-Z]+Service\(`
- **Message**: Use dependency injection via constructor instead of direct instantiation
- **Severity**: suggestion

### Facade Overuse
- **Pattern**: `use [^;]*\\Facades\\[^;]*;(?:[^;]*use [^;]*\\Facades\\[^;]*;){2,}`
- **Message**: Too many facades. Consider dependency injection for better testability
- **Severity**: suggestion

## Return Types

### Missing Return Types
- **Pattern**: `function [a-zA-Z0-9_]+\([^)]*\)\s*(?!:)`
- **Message**: Add return type hints to methods for better type safety
- **Severity**: suggestion

## Security

### Mass Assignment Vulnerability
- **Pattern**: `\$model->fill\(\$request->all\(\)|::create\(\$request->all\(\)\)`
- **Message**: Don't use request->all() directly with fill() or create(). Use validated() or specify allowed fields
- **Severity**: error

### CSRF Protection
- **Pattern**: `Route::post\([^)]*\)(?![^;]*->middleware\(['"']web['"']\))`
- **Message**: Ensure POST routes have CSRF protection (web middleware or @csrf directive)
- **Severity**: warning

## PHPDoc

### Missing Method Documentation
- **Pattern**: `public function [a-zA-Z0-9_]+\([^)]*\)(?!\s*:\s*\w+)\s*{(?![^}]*@param|[^}]*@return)`
- **Message**: Consider adding PHPDoc blocks for better code documentation
- **Severity**: suggestion
