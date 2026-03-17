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
        $course = Course::updateOrCreate(
            ['slug' => 'chess-basics'],
            [
                'title' => 'Chess Basics',
                'description' => '
                    <p>Every chess grandmaster started exactly where you are right now — staring at a board full of pieces, wondering what they all do.</p>
                    <br>
                    <p>In this course, you will meet every piece on the board one by one. You will see how each one moves, practice on real boards, and solve challenges that test what you have learned — just like a real chess training session.</p>
                    <br>
                    <p>By the time you finish, you will know every piece by heart and be ready to play your very first game of chess.</p>
                    <br>
                    <p>Your pieces are on the board. Let us begin.</p>
                ',
            ]
        );

        /*
        =========================
        CHAPTER 1 — MEET THE CHESS PIECES
        =========================
        */

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
                        'content' => '
                            <p>Meet the pawn — the foot soldier of your army.</p>
                            <br>
                            <p>You have <strong>8 of them</strong>, standing in a line at the front. They might look small, but do not let that fool you. Pawns protect your stronger pieces, control the center of the board, and carry a secret power that we will discover at the end of this lesson.</p>
                            <br>
                            <p>First, let us see how a pawn moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/Rf7GTWxj',
                            'instructions' => 'A pawn moves forward — one square at a time. Click the pawn and move it forward one square.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Good. Now here is a special rule:</p>
                            <br>
                            <p>On a pawn\'s <strong>very first move</strong>, it gets to choose — move one square forward as usual, or jump two squares forward to get into the game faster.</p>
                            <br>
                            <p>After that first move, it is back to one square at a time.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/ZK3XhnhN',
                            'instructions' => 'This pawn has not moved yet. Try moving it two squares forward — that is the special first-move option in action.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now for something important: pawns do not capture the same way they move.</p>
                            <br>
                            <p>Instead of taking a piece straight ahead, a pawn captures <strong>one square diagonally forward</strong> — to the left or to the right.</p>
                            <br>
                            <p>This means a pawn can be completely blocked from moving forward, but still threaten pieces beside it.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/iyEeRw9s',
                            'instructions' => 'An enemy piece is sitting on a diagonal square. Capture it by moving the pawn one square diagonally forward.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is the pawn\'s secret power: <strong>promotion</strong>.</p>
                            <br>
                            <p>If a pawn marches all the way to the other end of the board — the very last row — it transforms. You can turn it into any piece you want: a queen, a rook, a bishop, or a knight.</p>
                            <br>
                            <p>Almost every player chooses the queen, because she is the most powerful piece in the game. Promoting a pawn to a queen is one of the most exciting moments in chess.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/mAMHxWi3',
                            'instructions' => 'Guide the pawn forward to the last row. When it arrives, choose a piece to promote it to. What will you pick?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Pawns can never move backwards. Every step forward is permanent — so think before you push a pawn, because it cannot come back.</p>
                        '
                    ],
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
                        'content' => '
                            <p>The rook looks like a castle tower — and it plays like one too. It is one of the strongest pieces on the board.</p>
                            <br>
                            <p>You start with <strong>2 rooks</strong>, one in each corner. They begin tucked behind your pawns, but once those pawns move and the board opens up, the rooks become incredibly powerful.</p>
                            <br>
                            <p>Let us see how the rook moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/mgpyQnnU',
                            'instructions' => 'The rook slides in straight lines — left, right, up, or down. It can travel as many squares as it wants, as long as nothing is in the way. Explore the board and see how far the rook can reach.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is what to remember about the rook:</p>
                            <br>
                            <ul>
                                <li>-It moves in a <strong>straight line</strong> — horizontally or vertically.</li><br>
                                <li>-It can go <strong>as many squares as it likes</strong> in one direction.</li><br>
                                <li>-It <strong>cannot jump over</strong> other pieces. If something is in the way, it must stop.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-challenge',
                            'instructions' => 'Move the rook to the highlighted square. There are pieces in the way — you will need to find the right path.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Rooks love open lines. A rook trapped behind its own pawns does very little. As you learn to play, look for chances to move your pawns and free your rooks — that is when they truly come alive.</p>
                        '
                    ],
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
                        'content' => '
                            <p>The bishop is a long-range piece that strikes diagonally across the board.</p>
                            <br>
                            <p>You start with <strong>2 bishops</strong>. Look closely at where they begin — one starts on a light square, and the other starts on a dark square. That detail matters, and you will see why in a moment.</p>
                            <br>
                            <p>Let us see how the bishop moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/k4752fQ9',
                            'instructions' => 'The bishop moves diagonally — like an X shape spreading outward. Move it in different directions and notice which squares it visits.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Did you notice something? The bishop only ever lands on one color.</p>
                            <br>
                            <p>A bishop that starts on a light square will <strong>always</strong> stay on light squares. A bishop that starts on a dark square will always stay on dark squares. No matter how many moves it makes, it can never reach the other color.</p>
                            <br>
                            <p>Here is what to remember about the bishop:</p>
                            <br>
                            <ul>
                                <li>-It moves <strong>diagonally</strong> in any direction.</li><br>
                                <li>-It can go <strong>as many squares as it likes</strong> along a diagonal.</li><br>
                                <li>-It <strong>always stays on the same color</strong>.</li><br>
                                <li>-It <strong>cannot jump over</strong> other pieces.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/bishop-challenge',
                            'instructions' => 'Move the bishop to the highlighted square using diagonal moves. Plan your path before you move.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Your two bishops are a team. One covers light squares, the other covers dark squares. Together they can reach every corner of the board. Keeping both bishops active and unblocked is a sign of good chess.</p>
                        '
                    ],
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
                        'content' => '
                            <p>The knight is unlike any other piece on the board.</p>
                            <br>
                            <p>Every other piece moves in a straight line — forwards, backwards, sideways, or diagonally. The knight does none of that. It moves in an <strong>L-shape</strong>, and it is the only piece that can <strong>jump over</strong> other pieces in its way.</p>
                            <br>
                            <p>You start with <strong>2 knights</strong>. They are often the first pieces to come out at the start of a game — because unlike the rook or bishop, they do not need an open path to move.</p>
                            <br>
                            <p>Let us see the L-shape in action.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/eMYlOjbJ',
                            'instructions' => 'Watch the knight move in its L-shape: two squares in one direction, then one square to the side. Try all the different directions — from the center of the board, a knight can reach up to 8 different squares.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is a simple way to remember the knight\'s move:</p>
                            <br>
                            <p><strong>Two squares in any direction, then one square to the side.</strong></p>
                            <br>
                            <p>Two forward and one right. Two left and one down. Two backward and one left. Any combination works — as long as it makes the L-shape.</p>
                            <br>
                            <p>One more thing worth noticing: the knight always lands on the <strong>opposite color</strong> from where it started. Start on a light square, land on a dark square. Every single time.</p>
                            <br>
                            <p>And unlike every other piece, the knight <strong>jumps right over</strong> anything in its path — friendly pieces, enemy pieces, it does not matter.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-jump',
                            'instructions' => 'The board is crowded with pieces. Move the knight to the highlighted square — it will have to jump over pieces to get there. No other piece could make this move.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-challenge',
                            'instructions' => 'Can you get the knight to the highlighted square? Count the L carefully — there may be more than one way to get there.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The knight is the sneakiest attacker on the board. Because it jumps in an L and hops over pieces, it can threaten pieces that do not see it coming. When your opponent is focused on the rooks and bishops, the knight is often the one causing problems.</p>
                        '
                    ],
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
                        'content' => '
                            <p>The queen is the most powerful piece in chess — and you only get one.</p>
                            <br>
                            <p>Think back to what you learned about the rook and the bishop. The queen combines both of them. She moves like a rook along straight lines, and she moves like a bishop along diagonals. All in one piece, in any direction she chooses.</p>
                            <br>
                            <p>From the center of the board, the queen can reach more than half of all 64 squares in a single move. No other piece comes close.</p>
                            <br>
                            <p>Let us see her in action.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/CSED0dFK',
                            'instructions' => 'Move the queen in every direction — straight lines and diagonals. Try placing her in the center and see how many squares she can reach from that one spot.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is what to remember about the queen:</p>
                            <ul>
                                <li>She moves <strong>horizontally, vertically, or diagonally</strong> — any direction.</li><br>
                                <li>She can go <strong>as many squares as she likes</strong> in one direction.</li><br>
                                <li>She <strong>cannot jump over</strong> other pieces.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/queen-challenge',
                            'instructions' => 'Use the queen to capture all the highlighted pieces in as few moves as possible. She can reach all of them — but plan your route carefully.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The queen is powerful, but that power makes her a target. If you bring her out too early, your opponent will chase her around the board and gain time while you retreat. Save the queen for the right moment — and always keep her safe.</p>
                        '
                    ],
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
                        'content' => '
                            <p>The king is not the fastest piece, not the strongest piece, and not the most dangerous piece. But the king is the most important piece on the board.</p>
                            <br>
                            <p>Here is why: the entire game of chess is about one thing — trapping the enemy king. If your king is trapped, you lose. If you trap their king, you win. Every single move in chess, from the very first to the very last, is connected to this.</p>
                            <br>
                            <p>You each have <strong>1 king</strong>. Let us see how it moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/lkn6Ln0L',
                            'instructions' => 'The king moves one square at a time in any direction — forward, backward, sideways, or diagonally. Move the king around the board and explore all eight directions it can go.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is one very important rule: <strong>the king can never move into danger.</strong></p>
                            <br>
                            <p>If an enemy piece can capture on a square, your king is not allowed to step there. It does not matter if moving there seems useful — the king must always stay safe.</p>
                            <br>
                            <p>Now let us learn the two most important words in chess.</p>
                            <br>
                            <p><strong>Check</strong> means your king is under attack right now. When you are in check, you must deal with it immediately — you cannot ignore it and make a different move.<br><br>You have three ways to get out of check:</p>
                            <ul>
                                <li><strong>Move the king</strong> to a safe square.</li><br>
                                <li><strong>Block the attack</strong> by moving another piece in between.</li><br>
                                <li><strong>Capture the attacking piece.</strong></li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-in-check',
                            'instructions' => 'Your king is in check — it is being attacked right now. Find a way to get out of danger. Look for all three options: can you move the king, block the attack, or capture the piece giving check?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Checkmate</strong> is when the king is in check and there is absolutely no way to escape. You cannot move the king to safety, you cannot block the attack, and you cannot capture the attacker. The game ends immediately — the player who delivers checkmate wins.</p>
                            <br>
                            <p>Checkmate is the goal of the entire game. Everything you have learned in this course — every piece, every move — is building toward that one moment.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/checkmate-recognition',
                            'instructions' => 'Look at this position. The king is in check — but every square it could move to is also under attack, and there is no way to block or capture. This is checkmate. Explore the position and see why every exit is covered.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> In chess, we say the king is precious — not powerful. New players often forget about their king until it is too late. Make it a habit from your very first game: before every move, ask yourself — "Is my king safe?"</p>
                        '
                    ],
                ]
            ]
        );
    }
}