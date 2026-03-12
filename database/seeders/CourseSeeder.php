<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Course
        $course = Course::updateOrCreate(
            ['slug' => 'chess-basics'],
            [
                'title' => 'Chess Basics',
                'description' => 'Learn chess from the ground up. Understand the board, the pieces, and how the game works.',
            ]
        );

        // 2. Create Chapter
        $chapter = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'Meet the Chess Pieces']
        );

        /*
        =========================
        LESSON 1 — PAWN
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'pawn'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Pawn',
                'order' => 1,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the pawn is the most common piece in chess, and each player begins the game with eight pawns.</p>
                    <br>
                    <p>pawns may look small, but they are very important, they control space and help protect stronger pieces.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>pawns move forward one square at a time, however, on their very first move they may move two squares forward.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/ZK3XhnhN',
                            'instructions' => 'move the pawn forward two squares.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>pawns capture differently than they move, instead of capturing straight ahead, they capture one square diagonally.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/iyEeRw9s',
                            'instructions' => 'capture the pawn.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>pawns cannot move backward, they always march forward.</p>
                    <p>if a pawn reaches the other side of the board, it can be promoted into a stronger piece such as a queen.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/mAMHxWi3',
                            'instructions' => 'promote the pawn into a queen.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =========================
        LESSON 2 — ROOK
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'rook'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Rook',
                'order' => 2,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the rook looks like a castle tower, each player starts with two rooks placed at the corners of the board.</p>
                    <p>rooks are powerful pieces that control long straight lines.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>the rook moves horizontally or vertically, it can move any number of squares as long as nothing blocks its path.</p>
                    <p>however, the rook cannot jump over pieces.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/toJNO3I2',
                            'instructions' => 'observe how the rook moves.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =========================
        LESSON 3 — BISHOP
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'bishop'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Bishop',
                'order' => 3,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>each player starts the game with two bishops.</p>
                    <p>bishops move diagonally across the board and can travel as far as they want.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>a special rule about bishops is that they always stay on the same color squares.</p>
                    <p>if a bishop starts on a light square, it will always remain on light squares.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/3aKL0baG',
                            'instructions' => 'observe how the bishop moves.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =========================
        LESSON 4 — KNIGHT
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'knight'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Knight',
                'order' => 4,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the knight is the only piece shaped like a horse.</p>
                    <p>it moves differently from every other chess piece.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>the knight moves in an L-shape: two squares in one direction and then one square to the side.</p>
                    <p>knights also have a special ability. they can jump over other pieces.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/rdYsSMha',
                            'instructions' => 'observe how the knight moves.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =========================
        LESSON 5 — QUEEN
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'queen'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The Queen',
                'order' => 5,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the queen is the most powerful piece in chess.</p>
                    <p>she combines the movement of the rook and the bishop.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>this means the queen can move horizontally, vertically, and diagonally across the board.</p>
                    <p>because of this, losing your queen early can be a big disadvantage.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/pETxy6PB',
                            'instructions' => 'observe how the queen moves.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =========================
        LESSON 6 — KING
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'king'],
            [
                'chapter_id' => $chapter->id,
                'title' => 'The King',
                'order' => 6,
                'xp_reward' => 10,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the king is the most important piece in chess.</p>
                    <p>if your king is trapped and cannot escape attack, the game is over.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>the king moves one square in any direction: forward, backward, sideways, or diagonally.</p>
                    <p>even though the king moves slowly, protecting it is the most important goal in chess.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/Uwk35qqI',
                            'instructions' => 'observe how the king moves.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =====================================================
        COURSE 2 — OPENING PRINCIPLES
        =====================================================
        */

        $course2 = Course::updateOrCreate(
            ['slug' => 'opening-principles'],
            [
                'title' => 'Opening Principles',
                'description' => 'Master the fundamentals of chess openings. Learn how to control the center, develop your pieces, and keep your king safe in the first few moves.',
            ]
        );

        $chapter2 = Chapter::updateOrCreate(
            ['course_id' => $course2->id, 'order' => 1],
            ['title' => 'The Three Golden Rules']
        );

        Lesson::updateOrCreate(
            ['slug' => 'control-the-center'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Control the Center',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the center of the board consists of four squares: e4, d4, e5, and d5.</p>
                    <p>controlling those squares gives your pieces more room to move and limits your opponent\'s options.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>the best way to control the center is to place a pawn there early.</p>
                    <p>moves like 1.e4 or 1.d4 immediately stake a claim on the center and open lines for your pieces.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/ZK3XhnhN',
                            'instructions' => 'push a pawn to control the center.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'develop-your-pieces'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Develop Your Pieces',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>development means moving your pieces off their starting squares so they can participate in the game.</p>
                    <p>a piece sitting on its starting square is like a soldier still in the barracks — it cannot fight.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>aim to develop your knights and bishops before moving the same piece twice.</p>
                    <p>as a rule, avoid moving a piece more than once in the opening unless absolutely necessary.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/toJNO3I2',
                            'instructions' => 'develop your knights and bishops toward the center.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'castle-early'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Castle Early',
                'order' => 3,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>castling is a special move that tucks your king safely behind a wall of pawns.</p>
                    <p>it also activates your rook by moving it toward the center.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>to castle, you must not have moved your king or rook before, and there must be no pieces between them.</p>
                    <p>try to castle within the first 10 moves of the game.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/3aKL0baG',
                            'instructions' => 'castle your king to safety.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =====================================================
        COURSE 3 — BASIC TACTICS
        =====================================================
        */

        $course3 = Course::updateOrCreate(
            ['slug' => 'basic-tactics'],
            [
                'title' => 'Basic Tactics',
                'description' => 'Sharpen your tactical vision. Learn the most common patterns used to win material and create decisive advantages.',
            ]
        );

        $chapter3 = Chapter::updateOrCreate(
            ['course_id' => $course3->id, 'order' => 1],
            ['title' => 'Winning Material']
        );

        Lesson::updateOrCreate(
            ['slug' => 'the-fork'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Fork',
                'order' => 1,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>a fork is when one piece attacks two or more of the opponent\'s pieces at the same time.</p>
                    <p>the opponent can only save one, so you win the other.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>knights are especially dangerous forkers because of their unique L-shaped movement.</p>
                    <p>a knight fork attacking the king and queen at the same time is called a royal fork.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/rdYsSMha',
                            'instructions' => 'find the fork and win material.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'the-pin'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Pin',
                'order' => 2,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>a pin is when a piece cannot move without exposing a more valuable piece behind it to capture.</p>
                    <p>there are two types: an absolute pin (the piece behind is the king) and a relative pin (any other valuable piece).</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>bishops and rooks are the most common pieces used to create pins.</p>
                    <p>a pinned piece is often weak and can be attacked repeatedly.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/pETxy6PB',
                            'instructions' => 'identify and exploit the pin.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'the-skewer'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Skewer',
                'order' => 3,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>a skewer is the opposite of a pin.</p>
                    <p>a valuable piece is attacked directly and forced to move, leaving the less valuable piece behind it to be captured.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>skewers usually happen in the endgame when kings and rooks are exposed on open files and ranks.</p>
                    <p>always be cautious when your king or queen is on the same line as another piece.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/iyEeRw9s',
                            'instructions' => 'execute the skewer to win material.',
                        ]
                    ]
                ]
            ]
        );

        /*
        =====================================================
        COURSE 4 — CHECKMATE PATTERNS
        =====================================================
        */

        $course4 = Course::updateOrCreate(
            ['slug' => 'checkmate-patterns'],
            [
                'title' => 'Checkmate Patterns',
                'description' => 'Learn the most common checkmate patterns every chess player must know. Recognizing these patterns will help you finish games decisively.',
            ]
        );

        $chapter4 = Chapter::updateOrCreate(
            ['course_id' => $course4->id, 'order' => 1],
            ['title' => 'Classic Mates']
        );

        Lesson::updateOrCreate(
            ['slug' => 'back-rank-mate'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Back Rank Mate',
                'order' => 1,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>the back rank mate happens when a king is trapped on its back rank by its own pawns and a rook or queen delivers checkmate along that rank.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>this is one of the most common ways games are won at every level.</p>
                    <p>to avoid it, make a "luft" (escape square) for your king by pushing one of the pawns in front of it.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/mAMHxWi3',
                            'instructions' => 'deliver checkmate on the back rank.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'scholars-mate'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Scholar\'s Mate',
                'order' => 2,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>scholar\'s mate is a four-move checkmate that targets the f7 square, which is the weakest point in black\'s starting position.</p>
                    <p>it uses the queen and bishop working together to deliver fast checkmate.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>while scholar\'s mate is easy to defend against, knowing it helps you understand how to attack weak squares and coordinate your pieces.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/ZK3XhnhN',
                            'instructions' => 'execute scholar\'s mate in four moves.',
                        ]
                    ]
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'smothered-mate'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Smothered Mate',
                'order' => 3,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '<p>smothered mate is a checkmate delivered by a knight when the opponent\'s king is surrounded (smothered) by its own pieces.</p>'
                    ],
                    [
                        'type' => 'text',
                        'content' => '<p>because the king\'s own pieces block all its escape squares, the knight delivers checkmate with no way out.</p>
                    <p>this is one of the most beautiful and surprising patterns in chess.</p>'
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/rdYsSMha',
                            'instructions' => 'deliver the smothered mate with your knight.',
                        ]
                    ]
                ]
            ]
        );
    }
}
