<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@orbit.test'], ['name' => 'Alex Morgan', 'password' => 'OrbitDemo!2026', 'role' => 'admin']);
        $manager = User::firstOrCreate(['email' => 'manager@orbit.test'], ['name' => 'Jamie Chen', 'password' => 'OrbitDemo!2026', 'role' => 'manager']);
        $staff = User::firstOrCreate(['email' => 'staff@orbit.test'], ['name' => 'Taylor Brooks', 'password' => 'OrbitDemo!2026', 'role' => 'staff']);
        $names = ['Acme Studio', 'Northstar Labs', 'Forma Design', 'Evergreen Co.', 'Vertex Digital', 'Bloom & Co.'];
        $projects = ['Website redesign', 'Customer portal', 'Brand refresh', 'Mobile experience', 'Analytics platform', 'Spring campaign'];
        foreach ($names as $i => $name) {
            $client = Client::firstOrCreate(['email' => 'hello'.($i + 1).'@example.com'], ['name' => $name, 'company' => $name, 'phone' => '+1 415 555 01'.str_pad($i, 2, '0', STR_PAD_LEFT), 'contacts' => [['name' => 'Jordan Lee', 'email' => 'jordan'.($i + 1).'@example.com']], 'notes' => 'Primary account for our ongoing collaboration.']);
            $project = Project::firstOrCreate(['name' => $projects[$i], 'client_id' => $client->id], ['description' => 'A thoughtful, collaborative project focused on delivering a better customer experience.', 'status' => $i === 5 ? 'completed' : ($i === 3 ? 'planning' : 'active'), 'priority' => $i % 2 ? 'medium' : 'high', 'start_date' => today()->subDays(14), 'end_date' => today()->addDays(12 + $i * 7)]);
            $project->members()->syncWithoutDetaching([$admin->id, $manager->id, $staff->id]);
            foreach (['Research & discovery', 'Design key screens', 'Build the experience', 'Review and handoff'] as $j => $title) {
                $task = Task::firstOrCreate(['project_id' => $project->id, 'title' => $title], ['description' => 'Collaborate with the team, document decisions, and share progress.', 'assignee_id' => [$admin->id, $manager->id, $staff->id][$j % 3], 'status' => $i === 5 ? 'done' : ['done', 'in_progress', 'todo', 'review'][$j], 'priority' => $j === 1 ? 'high' : 'medium', 'due_date' => today()->addDays($j * 4 - 3)]);
                if (! $task->comments()->exists()) {
                    $task->comments()->create(['user_id' => $manager->id, 'body' => 'Let’s share an update before the next team check-in.']);
                }
            }
            Activity::firstOrCreate(['project_id' => $project->id, 'description' => 'Created '.$project->name], ['user_id' => $admin->id]);
        }
    }
}
