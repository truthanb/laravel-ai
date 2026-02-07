<?php

namespace Tests\Feature\Skills;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Laravel\Ai\Skills\SkillLoader;
use Laravel\Ai\Skills\SkillRegistry;
use Tests\TestCase;

class SkillIntegrationTest extends TestCase
{
    public function test_agent_can_use_skills_in_instructions(): void
    {
        // Arrange: Load skills
        $loader = new SkillLoader;
        $registry = new SkillRegistry;

        $skills = $loader->loadFromDirectory(__DIR__.'/Fixtures');
        foreach ($skills as $skill) {
            $registry->register($skill);
        }

        // Act: Create agent with skills
        $agent = new ExampleAgentWithSkills($registry);
        $instructions = $agent->instructions();

        // Assert: Skills are included in instructions
        $this->assertStringContainsString('<available_skills>', $instructions);
        $this->assertStringContainsString('pdf-processing', $instructions);
        $this->assertStringContainsString('data-analysis', $instructions);
        $this->assertStringContainsString('Extract text and tables from PDF files', $instructions);
    }

    public function test_agent_without_skills_has_clean_instructions(): void
    {
        // Arrange: Empty registry
        $registry = new SkillRegistry;

        // Act: Create agent without skills
        $agent = new ExampleAgentWithSkills($registry);
        $instructions = $agent->instructions();

        // Assert: No skills in instructions
        $this->assertStringNotContainsString('<available_skills>', $instructions);
        $this->assertStringContainsString('You are a helpful assistant', $instructions);
    }
}

/**
 * Example agent demonstrating skills integration.
 */
class ExampleAgentWithSkills implements Agent
{
    use Promptable;

    public function __construct(protected SkillRegistry $skills) {}

    public function instructions(): string
    {
        $instructions = 'You are a helpful assistant.';

        // Add available skills if any are registered
        if ($this->skills->count() > 0) {
            $instructions .= "\n\n".$this->skills->toPrompt();
        }

        return $instructions;
    }
}
