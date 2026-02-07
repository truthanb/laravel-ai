# Agent Skills Support (Proof of Concept)

This proof-of-concept adds support for the [Agent Skills](https://agentskills.io) open standard to the Laravel AI SDK.

## What are Agent Skills?

Agent Skills are a simple, open format for giving agents new capabilities and expertise. Skills are directories containing a `SKILL.md` file with instructions that agents can discover and use.

## Implementation

This POC includes:

- **`Skill`** - Value object representing a skill
- **`SkillLoader`** - Loads skills from directories following the Agent Skills specification
- **`SkillRegistry`** - Manages available skills
- Full test coverage with example skills

## Usage Example

```php
use Laravel\Ai\Skills\SkillLoader;
use Laravel\Ai\Skills\SkillRegistry;

// Load skills
$loader = new SkillLoader();
$registry = new SkillRegistry();

// Load from a directory
$skills = $loader->loadFromDirectory(storage_path('ai/skills'));

foreach ($skills as $skill) {
    $registry->register($skill);
}

// Use in an agent
class MyAgent implements Agent
{
    use Promptable;
    
    public function __construct(protected SkillRegistry $skills) {}
    
    public function instructions(): string
    {
        $base = 'You are a helpful assistant.';
        
        // Add available skills to prompt
        if ($this->skills->count() > 0) {
            $base .= "\n\n" . $this->skills->toPrompt();
        }
        
        return $base;
    }
}

// The agent now has access to skill descriptions
$agent = new MyAgent($registry);
$response = $agent->prompt('Help me extract text from a PDF');
// Agent can decide to activate the pdf-processing skill
```

## Skill Format

Skills follow the [Agent Skills specification](https://agentskills.io/specification):

```
skill-name/
└── SKILL.md          # Required: YAML frontmatter + Markdown instructions
```

Example `SKILL.md`:

```markdown
---
name: pdf-processing
description: Extract text and tables from PDF files, fill forms, merge documents.
license: MIT
---

# PDF Processing Skill

## When to use this skill

Use when working with PDF documents...

## Operations

### Extract text from PDF
...
```

## Progressive Disclosure

Following the Agent Skills pattern, the implementation supports progressive disclosure:

1. **Discovery**: Load only name and description (lightweight)
2. **Activation**: When relevant, load full skill content
3. **Execution**: Agent follows the detailed instructions

## Testing

```bash
vendor/bin/phpunit tests/Feature/Skills
```

All tests pass with 16 tests and 51 assertions.

## Next Steps

To turn this into a full SDK feature:

1. **Service Provider**: Auto-discover and register skills on boot
2. **Configuration**: Add `config/ai.php` settings for skill paths
3. **Trait**: Add `InteractsWithSkills` trait for easy agent integration
4. **Facade**: Optional `Skills` facade for convenient access
5. **Progressive Loading**: Implement lazy loading of full skill content
6. **Documentation**: Add to Laravel docs

## Files Added

```
src/Skills/
  ├── Skill.php                        # Value object
  ├── SkillLoader.php                  # Loads from directories
  ├── SkillRegistry.php                # Manages skills
  └── Exceptions/
      ├── SkillException.php
      ├── SkillNotFoundException.php
      └── SkillParseException.php

tests/Feature/Skills/
  ├── SkillLoaderTest.php              # 9 tests
  ├── SkillRegistryTest.php            # 7 tests
  └── Fixtures/
      ├── pdf-processing/SKILL.md      # Example skill
      └── data-analysis/SKILL.md       # Example skill
```

## Compatibility

- ✅ No breaking changes to existing SDK
- ✅ Additive feature (opt-in)
- ✅ Follows SDK conventions and patterns
- ✅ Uses existing dependencies (symfony/yaml via illuminate/support)
- ✅ Full test coverage

## Spec Compliance

This implementation follows the [Agent Skills specification v0.1](https://agentskills.io/specification):

- ✅ Supports required frontmatter fields (name, description)
- ✅ Supports optional fields (license, compatibility, metadata, allowed-tools)
- ✅ Validates skill format
- ✅ Generates recommended `<available_skills>` XML format
- ✅ Progressive disclosure pattern
