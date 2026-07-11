<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogGame;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create an admin author
        $author = User::where('is_admin', true)->first();
        if (!$author) {
            $author = User::first();
        }
        if (!$author) {
            $author = User::create([
                'name' => 'Vonchess Master',
                'email' => 'admin@vonchess.net',
                'password' => bcrypt('password123'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        }

        // 2. Sample Post 1: The Immortal Game
        $title1 = 'The immortal game of chess history';
        $slug1 = Str::slug($title1);
        
        // Only seed if it doesn't exist
        if (!Blog::where('slug', $slug1)->exists()) {
            $blog1 = Blog::create([
                'user_id' => $author->id,
                'title' => $title1,
                'slug' => $slug1,
                'summary' => 'An analysis of Adolf Anderssen\'s Immortal Game of 1851, demonstrating the beauty of romantic era piece sacrifices.',
                'content' => "# The Immortal Game\n\nThe **Immortal Game** was a chess game played by Adolf Anderssen and Lionel Kieseritzky on 21 June 1851 in London. The bold sacrifices made by Anderssen to secure victory have made it one of the most famous chess games of all time.\n\nIn this article, we look at the breathtaking combinational lines.\n\n## The Romantic Era of Chess\n\nDuring the 19th century, chess was dominated by attacking play and bold gambits. Defense was often considered secondary, and declining a sacrifice was sometimes viewed as cowardly!\n\nLet's take a look at the game below:\n\n[game:0]\n\n## Key Takeaways\n\n1. **King Safety**: Kieseritzky's queen moves early in the game, leaving his king undefended.\n2. **Development**: Anderssen focused on developing all his pieces, setting up the final mating net.\n3. **Sacrifice**: Material is secondary to mate! Any player who sacrifices their major pieces for a mating combination deserves to be remembered forever.",
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ]);

            BlogGame::create([
                'blog_id' => $blog1->id,
                'title' => 'Adolf Anderssen vs Lionel Kieseritzky (1851)',
                'pgn' => '[Event "London"]
[Site "London"]
[Date "1851.06.21"]
[Round "?"]
[White "Adolf Anderssen"]
[Black "Lionel Kieseritzky"]
[Result "1-0"]
[ECO "C33"]

1. e4 e5 2. f4 exf4 3. Bc4 Qh4+ 4. Kf1 b5 5. Bxb5 Nf6 6. Nf3 Qh6 7. d3 Nh5 8. Nh4 Qg5 9. Nf5 c6 10. g4 Nf6 11. Rg1 cxb5 12. h4 Qg6 13. h5 Qg5 14. Qf3 Ng8 15. Bxf4 Qf6 16. Nc3 Bc5 17. Nd5 Qxb2 18. Bd6 Bxg1 19. e5 Qxa1+ 20. Ke2 Na6 21. Nxg7+ Kd8 22. Qf6+ Nxf6 23. Be7# 1-0',
                'order' => 0,
            ]);
        }

        // 3. Sample Post 2: Mastering the King's Pawn Openings
        $title2 = 'Mastering the king\'s pawn openings';
        $slug2 = Str::slug($title2);

        if (!Blog::where('slug', $slug2)->exists()) {
            $blog2 = Blog::create([
                'user_id' => $author->id,
                'title' => $title2,
                'slug' => $slug2,
                'summary' => 'A beginner-friendly guide to understanding the classical Ruy Lopez opening and its strategic goals.',
                'content' => "# The Classical Ruy Lopez\n\nThe **Ruy Lopez**, also known as the Spanish Opening, is one of the oldest and most thoroughly analyzed chess openings. It begins with the moves:\n\n1. e4 e5\n2. Nf3 Nc6\n3. Bb5\n\nNamed after 16th-century Spanish priest Ruy López de Segura, this opening is highly popular at all levels.\n\n## Mainline Discussion\n\nWhite immediately puts pressure on Black's knight on c6, which defends the e5 pawn. In response, Black has many choices, the most common being the Morphy Defense `3... a6`.\n\nLet's walk through the mainline:\n\n[game:0]\n\n## Why play the Ruy Lopez?\n\n* **Strategic depth**: It teaches you middle game planning, pawn structures, and maneuvers.\n* **Open positions**: Leads to rich, complex battles that test both tactical awareness and positional understanding.",
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ]);

            BlogGame::create([
                'blog_id' => $blog2->id,
                'title' => 'The Classical Ruy Lopez - Mainline tutorial',
                'pgn' => '[Event "Ruy Lopez Tutorial"]
[Site "Vonchess Academy"]
[Date "2026.07.07"]
[White "Instructor"]
[Black "Student"]
[Result "*"]

1. e4 e5 2. Nf3 Nc6 3. Bb5 a6 4. Ba4 Nf6 5. O-O Be7 6. Re1 b5 7. Bb3 d6 8. c3 O-O 9. h3 Nb8 10. d4 Nbd7 *',
                'order' => 0,
            ]);
        }
    }
}
