<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class Chapter2Seeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'chess-basics')->firstOrFail();

        /*
        =========================
        CHAPTER 2 — SETTING UP THE BOARD
        =========================
        */

        $chapter = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'Setting Up the Board']
        );

        /*
        =========================
        LESSON 1 — THE BOARD
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-board'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Board',
                'order' => 1,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Before a single piece is placed, there is something every chess player needs to know — how to set up the board itself.</p>
                            <br>
                            <p>The chessboard is a grid of <strong>64 squares</strong>, arranged in 8 rows and 8 columns. The squares alternate between light and dark, and that pattern never changes.</p>
                            <br>
                            <p>Take a look at the board below.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/empty-board',
                            'instructions' => 'This is an empty chessboard. Count the squares along one side — there are 8. Now count along the other side — also 8. That gives us 64 squares in total. Notice how the light and dark squares alternate in every direction.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now here is the first rule of setting up a chessboard:</p>
                            <br>
                            <p><strong>The bottom-right square — the corner closest to your right hand — must always be a light square.</strong></p>
                            <br>
                            <p>A simple way to remember this: <strong>"Light on right."</strong></p>
                            <br>
                            <p>If you sit down at a board and the bottom-right corner is dark, the board is turned the wrong way. Rotate it 90 degrees and check again.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/board-orientation',
                            'instructions' => 'Look at the bottom-right corner of this board. Is it a light square or a dark square? If it is light, the board is set up correctly. This is how your board should always look before you start a game.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Getting the board orientation wrong is one of the most common mistakes beginners make — and it means every single piece ends up in the wrong place. Before every game, take one second to check: light on right. It will save you a lot of confusion.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        =========================
        LESSON 2 — RANKS AND FILES
        =========================

        COACHING STRUCTURE:
        1. Hook      — Chess has its own language; this is how players talk about squares
        2. See it    — Files (columns a–h), introduced first because letters come before numbers
        3. Practice  — Board: identify a file
        4. New idea  — Ranks (rows 1–8), always numbered from White's side
        5. Practice  — Board: identify a rank
        6. Combine   — How to name any square using file + rank
        7. Practice  — Board: find a named square (e.g. "find e4")
        8. Coach tip — Knowing square names lets you learn from books, videos, and coaches
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'ranks-and-files'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'Ranks and Files',
                'order' => 2,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Chess players have a way of talking about every square on the board. Instead of saying "the square in the middle," they can say exactly which square they mean — like a secret address system.</p>
                            <br>
                            <p>To read those addresses, you need to learn two things: <strong>files</strong> and <strong>ranks</strong>.</p>
                            <br>
                            <p>Let us start with files.</p>
                            <br>
                            <p>A <strong>file</strong> is a column — a line of squares going straight up and down the board. There are <strong>8 files</strong>, and each one has a letter name: <strong>a, b, c, d, e, f, g, h</strong> — going from left to right when you are playing as White.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/files-introduction',
                            'instructions' => 'The highlighted column is the e-file — every square in that column shares the name "e". Notice how the files run vertically, from the bottom of the board to the top. The letters go a, b, c, d, e, f, g, h from left to right.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now let us learn ranks.</p>
                            <br>
                            <p>A <strong>rank</strong> is a row — a line of squares going across the board from left to right. There are <strong>8 ranks</strong>, numbered <strong>1 through 8</strong>. Rank 1 is the row closest to White, and rank 8 is the row closest to Black.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/ranks-introduction',
                            'instructions' => 'The highlighted row is the 4th rank — every square in that row has the number 4 in its name. The ranks are numbered 1 to 8, starting from White\'s side at the bottom.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now put them together.</p>
                            <br>
                            <p>Every square on the board has a name made of <strong>one letter and one number</strong> — the file first, then the rank. For example:</p>
                            <br>
                            <ul>
                                <li>The square on the e-file, 4th rank = <strong>e4</strong></li><br>
                                <li>The square on the a-file, 1st rank = <strong>a1</strong></li><br>
                                <li>The square on the h-file, 8th rank = <strong>h8</strong></li>
                            </ul>
                            <br>
                            <p>Every one of the 64 squares has its own unique name. Let us practice finding them.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/square-names-practice',
                            'instructions' => 'A square is highlighted on the board. What is its name? Find the file (the letter, a–h) and the rank (the number, 1–8) — then put them together. File first, rank second.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Knowing square names is how chess players communicate. When you watch a chess video, read a chess book, or get advice from a coach, they will use square names constantly — "put your knight on f3," "attack the d5 square," "your king is safer on g1."</p>
                        '
                    ],
                ]
            ]
        );

        /*
        =========================
        LESSON 3 — PLACING THE PIECES
        =========================

        COACHING STRUCTURE:
        1. Hook      — You know the pieces, you know the board; now put it all together
        2. Core idea — Pawns always go on the 2nd rank (for White) and 7th rank (for Black)
        3. See it    — Board showing pawns placed
        4. Core idea — The back rank: introduce piece order from the outside in (rooks → knights → bishops)
        5. See it    — Board showing rooks, knights, bishops placed
        6. Key rule  — Queen on her color, king on the remaining square
        7. See it    — Board showing completed setup
        8. Practice  — Board: player places pieces themselves
        9. Coach tip — The setup is always the same; knowing it by heart means you are ready to play anywhere
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'placing-the-pieces'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'Placing the Pieces',
                'order' => 3,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You know every piece. You know the board. Now it is time to put it all together and set up a real game of chess.</p>
                            <br>
                            <p>Every chess game starts from the exact same position — the same pieces, the same squares, every time. Once you know the setup, you will never have to think about it again.</p>
                            <br>
                            <p>Let us build it step by step.</p>
                            <br>
                            <p><strong>Step 1: Place the pawns.</strong></p>
                            <br>
                            <p>Each player has 8 pawns, and they all go on the same rank — the row directly in front of the back row. For White, that is the <strong>2nd rank</strong>. For Black, that is the <strong>7th rank</strong>. One pawn on every square across the row.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/setup-pawns',
                            'instructions' => 'White\'s pawns fill the entire 2nd rank, and Black\'s pawns fill the entire 7th rank — one pawn per square, all the way across. This is where all 16 pawns begin every single game.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Step 2: Place the rooks, knights, and bishops.</strong></p>
                            <br>
                            <p>The remaining pieces go on the back rank — rank 1 for White and rank 8 for Black.</p>
                            <br>
                            <ul>
                                <li><strong>Corners</strong> — Rooks go in the four corners: a1, h1 for White; a8, h8 for Black.</li><br>
                                <li><strong>Next to the rooks</strong> — Knights go beside them: b1, g1 for White; b8, g8 for Black.</li><br>
                                <li><strong>Next to the knights</strong> — Bishops go beside them: c1, f1 for White; c8, f8 for Black.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/setup-rooks-knights-bishops',
                            'instructions' => 'Rooks in the corners, knights beside them, bishops beside the knights. Notice how the back rank is filling in from the outside toward the center — two squares in the middle are still empty. That is where the queen and king go.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Step 3: Place the queen and king.</strong></p>
                            <br>
                            <p>Two squares remain in the center — d1 and e1 for White (d8 and e8 for Black). Here is how to remember which piece goes where:</p>
                            <br>
                            <p><strong>The queen always goes on her own color.</strong> The White queen goes on a light square (d1). The Black queen goes on a dark square (d8).</p>
                            <br>
                            <p>The king takes the last remaining square — e1 for White, e8 for Black.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/setup-complete',
                            'instructions' => 'The board is now fully set up and ready to play. White\'s queen is on d1 — a light square. Black\'s queen is on d8 — a dark square. The kings face each other directly across the board. This is the starting position of every chess game ever played.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/setup-practice',
                            'instructions' => 'The board is empty. Place every piece in its correct starting square. Work from the outside in — rooks in the corners, then knights, then bishops, then the queen on her color, and the king on the last square. Take your time.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The starting position is always identical — every game, every board, everywhere in the world. Once you know it by heart, you are ready to sit down and play anywhere. Practice setting it up until you can do it without thinking.</p>
                        '
                    ],
                ]
            ]
        );
    }
}