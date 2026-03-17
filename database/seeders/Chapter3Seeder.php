<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class Chapter3Seeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'chess-basics')->firstOrFail();

        /*
        =========================
        CHAPTER 3 — SPECIAL MOVES
        =========================

        LESSON BREAKDOWN:
        Lesson 1 — Pawn Promotion      : All four options, when to underpromote, why it changes the game
        Lesson 2 — Castling            : What it is, kingside vs queenside, the five conditions
        Lesson 3 — Check & Checkmate   : Three ways out of check, stalemate, recognizing checkmate patterns
        Lesson 4 — En Passant          : Why the rule exists, the trigger condition, the timing rule

        ORDER RATIONALE:
        Promotion first — familiar from Chapter 1, good confidence-builder to open the chapter.
        Castling second — common in real games, moderate complexity.
        Check & Checkmate third — deepens knowledge from the King lesson, introduces stalemate.
        En Passant last — the hardest and least common rule in chess; saved for when students
        are warmed up and comfortable with the chapter's rhythm.
        =========================
        */

        $chapter = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 3],
            ['title' => 'Special Moves']
        );

        /*
        =========================
        LESSON 1 — PAWN PROMOTION
        =========================

        COACHING STRUCTURE:
        1. Hook       — Remind students of the pawn's secret power from Chapter 1
        2. Recap      — What promotion is and when it happens
        3. See it     — Board showing a pawn reaching the last rank and promoting
        4. All options — Explain all four choices: queen, rook, bishop, knight
        5. Key idea   — Almost always choose the queen, but not always
        6. See it     — Board showing underpromotion scenario (promote to knight to win)
        7. Practice   — Board: promote the pawn, then use the new piece to finish the job
        8. Coach tip  — Every pawn is a queen in disguise; that is why pawns matter
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'pawn-promotion'],
            [
                'chapter_id' => $chapter->id,
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
                            'lichessUrl' => 'https://lichess.org/study/embed/DKy0YYJs/mAMHxWi3',
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/underpromotion',
                            'instructions' => 'Look at this position carefully. If the pawn promotes to a queen, the game continues. But if it promotes to a knight, it delivers checkmate immediately — the knight lands on a square the enemy king cannot escape. Try promoting to a knight and see what happens.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/promotion-challenge',
                            'instructions' => 'Guide the pawn to the last rank, then use your newly promoted piece to deliver checkmate. Choose your promotion wisely — think about which piece will be most useful in this position.',
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

        /*
        =========================
        LESSON 2 — CASTLING
        =========================

        COACHING STRUCTURE:
        1. Hook       — The king is safest tucked away; castling is how you get it there
        2. What it is — Two pieces moving in one move: the only time this is allowed
        3. See it     — Board showing kingside castling step by step
        4. Variation  — Queenside castling and how it differs
        5. See it     — Board showing queenside castling
        6. The rules  — Five conditions that must ALL be met (presented after seeing it, not before)
        7. Practice   — Board: castle in the right direction given the position
        8. Coach tip  — Castle early; an uncastled king in the center is a target
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'castling'],
            [
                'chapter_id' => $chapter->id,
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/kingside-castling',
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/queenside-castling',
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/castling-conditions',
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

        /*
        =========================
        LESSON 3 — CHECK & CHECKMATE
        =========================

        COACHING STRUCTURE:
        1. Hook       — They know the words; now they need to truly understand them
        2. Recap      — What check is, briefly
        3. The three  — Three ways out of check, each explained and demonstrated separately
        4. Practice   — Board: escape check (all three methods available; find them)
        5. New idea   — Checkmate: when all three exits are closed at once
        6. See it     — Board: explore a checkmate position and identify why each exit is blocked
        7. New idea   — Stalemate: the trap that saves a losing player (introduce contrast)
        8. See it     — Board: stalemate position, compare to checkmate
        9. Practice   — Board: distinguish check, checkmate, and stalemate in three positions
        10. Coach tip — Always check all three exits before deciding a position is checkmate
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'check-and-checkmate'],
            [
                'chapter_id' => $chapter->id,
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/three-ways-out',
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/checkmate-analysis',
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/stalemate-vs-checkmate',
                            'instructions' => 'Compare these two positions side by side. In one, the king is in check with no escape — that is checkmate. In the other, the king is not in check but has no legal moves — that is stalemate. Spot the difference: is the king being attacked or not?',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/check-checkmate-stalemate-quiz',
                            'instructions' => 'Three positions, three different situations. For each one, decide: is this check, checkmate, or stalemate? Use what you have learned — look at whether the king is attacked, and whether any escape is possible.',
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

        /*
        =========================
        LESSON 4 — EN PASSANT
        =========================

        COACHING STRUCTURE:
        1. Hook       — Frame it honestly: this is the strangest rule in chess, and there is a reason for it
        2. The why    — Explain the historical context: the two-square pawn move created an unfair escape
        3. The what   — What en passant is, described in plain language before any board
        4. See it     — Board showing the trigger moment (enemy pawn advances two squares)
        5. See it     — Board showing the en passant capture itself
        6. The timing — The single most important rule: it must be done immediately or the right is lost
        7. Practice   — Board: recognize and execute en passant before the opportunity disappears
        8. Challenge  — Board: position where en passant is available but so are other moves; choose correctly
        9. Coach tip  — You will not see en passant every game, but you will see it; be ready
        =========================
        */

        Lesson::updateOrCreate(
            ['slug' => 'en-passant'],
            [
                'chapter_id' => $chapter->id,
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
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/en-passant-trigger',
                            'instructions' => 'Watch what happens here. The Black pawn on the right advances two squares, landing right beside the White pawn. Without en passant, White cannot capture it — the Black pawn has slipped past. This is the moment that triggers the en passant rule.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/en-passant-capture',
                            'instructions' => 'Now execute the en passant capture. Move the White pawn diagonally forward — as if the Black pawn had only moved one square. The Black pawn is removed from the square it actually landed on, not the square White moved to. This is the only move in chess where a piece is captured from a different square than where the capturing piece lands.',
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
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/en-passant-timing',
                            'instructions' => 'The Black pawn has just moved two squares. You have one move to capture en passant — right now. If you move anything else first, the chance disappears. Find the en passant capture and take it.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/en-passant-challenge',
                            'instructions' => 'En passant is available — but so are other moves. Is capturing en passant the best choice here, or should you do something else? Think about what each option gives you, then make your decision.',
                        ]
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
    }
}