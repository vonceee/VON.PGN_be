<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class Chapter4Seeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'chess-basics')->firstOrFail();

        /*
        =========================
        CHAPTER 4 — COURSE SUMMARY
        =========================
        */

        $chapter = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 4],
            ['title' => 'Course Summary']
        );

        /*
        =========================
        LESSON 1 — YOU ARE READY
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'you-are-ready'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'You Are Ready',
                'order' => 1,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>When you started this course, you had a chessboard in front of you and pieces you did not fully understand.</p>
                            <br>
                            <p>That is no longer true.</p>
                            <br>
                            <p>You have covered everything a player needs to sit down at any chessboard in the world and play a complete, legal game of chess. That is not a small thing. Let us take a moment to look back at what you actually learned.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 1 — The Pieces</h3>
                            <br>
                            <p>You met all six pieces. Here is the one thing to carry forward about each one:</p>
                            <br>
                            <ul>
                                <li><strong>Pawn</strong> — Moves forward, captures diagonally, and can become a queen. Never underestimate it.</li><br>
                                <li><strong>Rook</strong> — Dominates straight lines. Most powerful when the board is open.</li><br>
                                <li><strong>Bishop</strong> — Controls diagonals and never changes color. Your two bishops cover the whole board together.</li><br>
                                <li><strong>Knight</strong> — The only piece that jumps. Moves in an L-shape and is the sneakiest attacker on the board.</li><br>
                                <li><strong>Queen</strong> — The most powerful piece. Combines the rook and bishop. Keep her safe.</li><br>
                                <li><strong>King</strong> — The most important piece. The entire game is fought around protecting yours and trapping theirs.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 2 — The Board</h3>
                            <br>
                            <p>You learned that the board is not just a surface — it has its own structure and language.</p>
                            <br>
                            <ul>
                                <li><strong>Light on right.</strong> The bottom-right corner is always a light square. If it is not, rotate the board.</li><br>
                                <li><strong>Files run vertically</strong> (a through h), <strong>ranks run horizontally</strong> (1 through 8). Every square has a unique name — file first, rank second.</li><br>
                                <li><strong>The starting position is always the same.</strong> Rooks in the corners, knights beside them, bishops beside the knights, queen on her color, king on the last square — and pawns across the entire second rank.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 3 — Special Moves</h3>
                            <br>
                            <p>You learned the four rules that most beginners do not know — and that now set you apart.</p>
                            <br>
                            <ul>
                                <li><strong>Pawn promotion</strong> — A pawn that reaches the last rank transforms into any piece. Almost always choose the queen, but a knight can sometimes win the game immediately when a queen cannot.</li><br>
                                <li><strong>Castling</strong> — The king and rook move together in one turn. It is the fastest way to get your king to safety. Castle early.</li><br>
                                <li><strong>Check and checkmate</strong> — Check means your king is under attack and you must deal with it immediately. Checkmate means there is no escape and the game is over. Stalemate — when a player has no legal moves but is not in check — is a draw, not a win.</li><br>
                                <li><strong>En passant</strong> — A pawn that has reached the 5th rank can capture an enemy pawn that just moved two squares beside it. It must be done immediately or the right is lost forever.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/starting-position-final',
                            'instructions' => 'This is the starting position of chess — the same position that has begun every chess game ever played, from beginners to world champions. You now know what every piece on this board does, where it belongs, and what it is capable of.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Three Habits to Carry Into Your First Game</h3>
                            <br>
                            <p>Rules are one thing. Playing well is another. Here are three habits that will make you a better player from your very first game — habits that strong players never stop practicing:</p>
                            <br>
                            <p><strong>1. Before every move, check if your king is safe.</strong> Do not just think about what you are doing — think about what your opponent can do back. A single moment of forgetting the king can end a game instantly.</p>
                            <br>
                            <p><strong>2. Think before you touch a piece.</strong> In a real game, touching a piece means you must move it. Get into the habit of deciding your move in your head first, then reaching for the piece. Slow down. Chess rewards patience.</p>
                            <br>
                            <p><strong>3. Try to control the center.</strong> The four squares in the middle of the board — e4, d4, e5, d5 — are the most valuable squares on the board. Pieces placed in the center have more options and more power. From your very first move, think about how you can influence those squares.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>What Comes Next</h3>
                            <br>
                            <p>You have learned how chess works. What comes next is learning how to play it well — and that is where the game truly begins.</p>
                            <br>
                            <p>Here is what is waiting for you:</p>
                            <br>
                            <ul>
                                <li><strong>Opening principles</strong> — The first few moves of a game follow patterns that strong players have developed over centuries. Learning them will give you a strong, confident start every time you sit down to play.</li><br>
                                <li><strong>Tactics</strong> — Short sequences of moves that win material or deliver checkmate. Forks, pins, skewers, discovered attacks — these are the weapons of chess, and they are learnable.</li><br>
                                <li><strong>Checkmate patterns</strong> — There are classic checkmating patterns that appear in real games over and over. Learning to recognize them means you will spot the winning move when it appears.</li><br>
                                <li><strong>Playing real games</strong> — Everything above becomes clearer the more you play. Win, lose, or draw — every game teaches you something a lesson cannot.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Chess is one of the oldest games in the world. People have been playing it for over 1,500 years, and they are still discovering new ideas in it today. You have just joined that long tradition.</p>
                            <br>
                            <p>You know the pieces. You know the board. You know the rules.</p>
                        '
                    ],
                ]
            ]
        );
    }
}