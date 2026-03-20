<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class ChessBasicsSeeder extends Seeder
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

        $chapter1 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'Meet the Chess Pieces']
        );

        Lesson::updateOrCreate(
            ['slug' => 'pawn'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/Rf7GTWxj',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/ZK3XhnhN',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/iyEeRw9s',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/mAMHxWi3',
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

        Lesson::updateOrCreate(
            ['slug' => 'rook'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/mgpyQnnU',
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
                    // [
                    //     'type' => 'board',
                    //     'task' => [
                    //         'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-challenge',
                    //         'instructions' => 'Move the rook to the highlighted square. There are pieces in the way — you will need to find the right path.',
                    //     ]
                    // ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Rooks love open lines. A rook trapped behind its own pawns does very little. As you learn to play, look for chances to move your pawns and free your rooks — that is when they truly come alive.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'bishop'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/k4752fQ9',
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
                    // [
                    //     'type' => 'board',
                    //     'task' => [
                    //         'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/bishop-challenge',
                    //         'instructions' => 'Move the bishop to the highlighted square using diagonal moves. Plan your path before you move.',
                    //     ]
                    // ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Your two bishops are a team. One covers light squares, the other covers dark squares. Together they can reach every corner of the board. Keeping both bishops active and unblocked is a sign of good chess.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'knight'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/eMYlOjbJ',
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
                    // [
                    //     'type' => 'board',
                    //     'task' => [
                    //         'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-jump',
                    //         'instructions' => 'The board is crowded with pieces. Move the knight to the highlighted square — it will have to jump over pieces to get there. No other piece could make this move.',
                    //     ]
                    // ],
                    // [
                    //     'type' => 'board',
                    //     'task' => [
                    //         'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-challenge',
                    //         'instructions' => 'Can you get the knight to the highlighted square? Count the L carefully — there may be more than one way to get there.',
                    //     ]
                    // ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The knight is the sneakiest attacker on the board. Because it jumps in an L and hops over pieces, it can threaten pieces that do not see it coming. When your opponent is focused on the rooks and bishops, the knight is often the one causing problems.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'queen'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/CSED0dFK',
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
                    // [
                    //     'type' => 'board',
                    //     'task' => [
                    //         'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/queen-challenge',
                    //         'instructions' => 'Use the queen to capture all the highlighted pieces in as few moves as possible. She can reach all of them — but plan your route carefully.',
                    //     ]
                    // ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The queen is powerful, but that power makes her a target. If you bring her out too early, your opponent will chase her around the board and gain time while you retreat. Save the queen for the right moment — and always keep her safe.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'king'],
            [
                'chapter_id' => $chapter1->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/lkn6Ln0L',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/YbYvCtBp',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/c8MQQdhB',
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

        $chapter2 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'Setting Up the Board']
        );

        Lesson::updateOrCreate(
            ['slug' => 'the-board'],
            [
                'chapter_id' => $chapter2->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/cLBtVq1S',
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
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Getting the board orientation wrong is one of the most common mistakes beginners make — and it means every single piece ends up in the wrong place. Before every game, take one second to check: light on right. It will save you a lot of confusion.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'ranks-and-files'],
            [
                'chapter_id' => $chapter2->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/HaS732BO',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/RnfSqa5W',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/aeflTLnt',
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

        Lesson::updateOrCreate(
            ['slug' => 'placing-the-pieces'],
            [
                'chapter_id' => $chapter2->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/nG77y3GR',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/RozFzaca',
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/CajNz0xv',
                            'instructions' => 'The board is now fully set up and ready to play. White\'s queen is on d1 — a light square. Black\'s queen is on d8 — a dark square. The kings face each other directly across the board. This is the starting position of every chess game ever played.',
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

        $chapter3 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 3],
            ['title' => 'Special Moves']
        );

        Lesson::updateOrCreate(
            ['slug' => 'pawn-promotion'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'Pawn Promotion',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Back in Chapter 1, you learned the pawn\'s secret power — if it marches all the way to the other end of the board, it transforms into a stronger piece.</p>
                            <br>
                            <p>That power is called <strong>promotion</strong>, and it is one of the most game-changing moments in chess. Now let us look at it properly.</p>
                            <br>
                            <p>Promotion happens the moment a pawn steps onto the <strong>last rank</strong> — rank 8 for White, rank 1 for Black. The pawn is immediately removed from the board and replaced with a new piece of your choice.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/9PedRmNR',
                            'instructions' => 'Move the pawn forward to the last rank. The moment it arrives, you will be asked to choose a new piece. Watch how it transforms.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You have four choices when a pawn promotes:</p>
                            <br>
                            <ul>
                                <li><strong>Queen</strong></li><br>
                                <li><strong>Rook</strong></li><br>
                                <li><strong>Bishop</strong></li><br>
                                <li><strong>Knight</strong></li>
                            </ul>
                            <br>
                            <p>So why would anyone ever pick something other than a queen?</p>
                            <br>
                            <p>Sometimes — not often, but it happens — promoting to a <strong>knight</strong> wins the game immediately, when promoting to a queen would not. This is called <strong>underpromotion</strong>. The knight\'s L-shape can reach squares a queen simply cannot threaten in the same move.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/OZXAwmHB',
                            'instructions' => 'Look at this position carefully. If the pawn promotes to a queen, the game continues. But if it promotes to a knight, it delivers checkmate immediately — the knight lands on a square the enemy king cannot escape. Try promoting to a knight and see what happens.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Experienced players say every pawn is a queen in disguise. That is why pushing passed pawns — pawns with no enemy pawns blocking their path — is one of the most powerful strategies in chess. Never underestimate a pawn that has a clear road ahead of it.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'castling'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'Castling',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The king starts the game in the center of the back rank — and that is a dangerous place to be. As the game opens up and pieces start attacking, a king stuck in the middle becomes an easy target.</p>
                            <br>
                            <p>Castling is the solution. It is a special move that does two things at once: it tucks the king safely into the corner, and it brings a rook out to the center where it can be useful. Two pieces, one move.</p>
                            <br>
                            <p>It is the only move in chess where two pieces move at the same time.</p>
                            <br>
                            <p>Let us see how it works. There are two versions — <strong>kingside castling</strong> and <strong>queenside castling</strong>.</p>
                            <br>
                            <p><strong>Kingside castling</strong> happens on the right side of the board (where the king starts). The king slides two squares toward the rook, and the rook hops over the king and lands right beside it.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/6k0TUlDp',
                            'instructions' => 'The king moves two squares to the right, toward the h-file rook. The rook then jumps over the king and lands on f1 — right beside it. In one move, the king is tucked safely in the corner. Try castling kingside.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Queenside castling</strong> happens on the left side of the board (where the queen started). The king slides two squares toward the rook, and the rook hops over to land right beside it — just like before, but on the other side.</p>
                            <br>
                            <p>Queenside castling moves the king slightly less far from the center, which is why most players prefer kingside castling — but queenside is perfectly valid and often used in aggressive play.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/mb91pEwb',
                            'instructions' => 'This time, castle queenside. The king moves two squares to the left, toward the a-file rook. The rook jumps over and lands on d1. Notice the king ends up on c1 — one square further from the edge than with kingside castling.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Castling is powerful, but it comes with strict rules. <strong>All five conditions must be met</strong> — if even one is not, castling is not allowed.</p>
                            <br>
                            <ul>
                                <li><strong>The king has not moved.</strong> If your king has moved even once during the game, castling is no longer possible — ever, for that game.</li><br>
                                <li><strong>The rook has not moved.</strong> The same rule applies to the rook you are castling with. If it has moved, you cannot castle with it.</li><br>
                                <li><strong>No pieces between them.</strong> Every square between the king and the rook must be empty.</li><br>
                                <li><strong>The king is not in check.</strong> You cannot castle to escape a check.</li><br>
                                <li><strong>The king does not pass through check.</strong> Every square the king travels through must be safe — not under attack by any enemy piece.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/7Z7jVKdK',
                            'instructions' => 'In this position, one side can castle and one cannot. Look at both kings and their rooks — check the five conditions. Which side is able to castle? Castle with the correct side.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> One of the most important habits in chess is to castle early — ideally within the first ten moves. A king left in the center is exposed to attacks down open files. The moment you can castle, ask yourself whether you should. Most of the time, the answer is yes.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'check-and-checkmate'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'Check and Checkmate',
                'order' => 3,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You already know the words <strong>check</strong> and <strong>checkmate</strong> from when you met the king. Now it is time to go deeper — because knowing the words is not the same as knowing how to handle them in a real game.</p>
                            <br>
                            <p>Let us start with check.</p>
                            <br>
                            <p><strong>Check</strong> means your king is being attacked by an enemy piece right now. When you are in check, you must get out of it immediately. You cannot make any other move — not advancing a pawn, not developing a piece, not attacking the opponent. Getting out of check is the only priority.</p>
                            <br>
                            <p>There are exactly <strong>three ways</strong> to escape check. Let us look at each one.</p>
                            <br>
                            <p><strong>1. Move the king</strong> — step the king to a square that is not under attack. This is the most obvious escape and the one beginners reach for first.</p>
                            <br>
                            <p><strong>2. Block the check</strong> — place one of your own pieces between the attacking piece and the king. This only works against pieces that attack along a line — rooks, bishops, and queens. A knight check cannot be blocked.</p>
                            <br>
                            <p><strong>3. Capture the attacker</strong> — take the piece that is giving check. Any of your pieces can do this, including the king itself, as long as the square is safe.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/GgpQ7oIY',
                            'instructions' => 'Your king is in check. Look carefully — all three escapes are available in this position. Can you find each one? Try moving the king, then try blocking, then try capturing the attacker. Notice how each one resolves the check differently.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Now for the moment the entire game has been building toward: <strong>checkmate</strong>.</p>
                            <br>
                            <p>Checkmate is check with no escape. The king is under attack, and all three exits are closed at the same time — the king has no safe square to move to, the check cannot be blocked, and the attacker cannot be captured. The game ends immediately.</p>
                            <br>
                            <p>Let us look at a checkmate position and verify each exit is truly closed.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/BES4J6db',
                            'instructions' => 'This position is checkmate. Do not just take our word for it — check each exit yourself. Can the king move anywhere safe? Can the check be blocked? Can the attacker be captured? Work through each question and you will see why there is no escape.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is one more outcome worth knowing — and it catches beginners off guard more than almost anything else: <strong>stalemate</strong>.</p>
                            <br>
                            <p>Stalemate happens when a player has no legal moves and their king is <strong>not</strong> in check. The king is not being attacked, but every square it could move to is under attack — and no other pieces can move either. The game ends immediately as a <strong>draw</strong> — nobody wins.</p>
                            <br>
                            <p>This matters because stalemate can save a losing player. A player who is about to lose on material can sometimes force their opponent into stalemating them by accident — turning a loss into a draw. Beginners accidentally stalemate their opponents all the time when they are winning.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/faENBbBD',
                            'instructions' => 'The black king is not in check but has no legal moves — that is stalemate.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before you declare checkmate, always verify all three exits are closed. New players sometimes miss a blocking move or a capture that saves the king. Check every square the king can reach, check every piece that could block, and check whether the attacker can be taken. Only when all three are impossible is it truly checkmate.</p>
                        '
                    ],
                ]
            ]
        );

        Lesson::updateOrCreate(
            ['slug' => 'en-passant'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'En Passant',
                'order' => 4,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>This is the strangest rule in chess. Even experienced players sometimes forget it exists. But it is a real, official rule — and once you understand why it was created, it will make perfect sense.</p>
                            <br>
                            <p>Let us start with the problem that created en passant.</p>
                            <br>
                            <p>You already know that a pawn can move two squares forward on its very first move. That rule was added to speed up the opening of the game. But it created an unfair situation: a pawn could use its two-square jump to sneak past an enemy pawn that was positioned to capture it.</p>
                            <br>
                            <p>Imagine your pawn has marched all the way to the 5th rank — deep in enemy territory. Normally, if an opponent\'s pawn were one square ahead and beside you, you could capture it diagonally. But if that pawn jumps two squares to land right beside you instead of one square ahead, it escapes your capture entirely. That felt unfair to players, and so <strong>en passant</strong> was created to fix it.</p>
                            <br>
                            <p><strong>En passant</strong> is a special pawn capture. It allows your pawn to capture an enemy pawn that has just moved two squares forward — even though that pawn is now beside you, not diagonally ahead of you. You capture it as if it had only moved one square.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/TH1ceo90',
                            'instructions' => 'Watch what happens here. The Black pawn on the right advances two squares, landing right beside the White pawn. Without en passant, White cannot capture it — the Black pawn has slipped past. This is the moment that triggers the en passant rule.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Here is the most important rule about en passant — and the one most often forgotten:</p>
                            <br>
                            <p><strong>En passant must be played immediately.</strong></p>
                            <br>
                            <p>The moment the enemy pawn advances two squares, you have exactly one chance to capture en passant — on your very next move. If you make any other move first, the right to capture en passant is gone forever. You cannot come back to it one move later.</p>
                            <br>
                            <p>To summarize the full rule:</p>
                            <ul>
                                <li>Your pawn must be on the <strong>5th rank</strong>.</li>
                                <li>An enemy pawn must have just moved <strong>two squares forward</strong> to land directly beside yours.</li>
                                <li>You must capture <strong>on your very next move</strong> — or the opportunity is gone.</li>
                                <li>Your pawn moves <strong>diagonally forward</strong> to the square the enemy pawn skipped over, and the enemy pawn is removed.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> En passant will not come up in every game — but it will come up. The players who are not ready for it either miss the chance to use it, or do not realize their opponent can use it against them. Now that you know it exists and exactly when it happens, you will never be caught off guard.</p>
                        '
                    ],
                ]
            ]
        );

        $chapter4 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 4],
            ['title' => 'Course Summary']
        );

        Lesson::updateOrCreate(
            ['slug' => 'you-are-ready'],
            [
                'chapter_id' => $chapter4->id,
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
                            'lichessUrl' => 'https://lichess.org/study/DKy0YYJs/ZZ7kxutX',
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
