<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;

class MiddlegamePrinciplesSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::updateOrCreate(
            ['slug' => 'middlegame-principles'],
            [
                'title' => 'Middlegame Principles',
                'description' => '
                    <p>The opening is over. Your pieces are developed, your king is safe, and the real game is about to begin.</p>
                    <br>
                    <p>The middlegame is where chess becomes chess. There are no simple rules to follow here — just positions to understand, plans to form, and decisions to make. It is the most complex phase of the game, and the most rewarding to learn.</p>
                    <br>
                    <p>In this course, you will learn how to make your pieces work together, how to read pawn structures, how to spot and use the four most important tactical weapons in chess, and how to launch and survive attacks.</p>
                    <br>
                    <p>By the end, you will not just be reacting to what your opponent does. You will have a plan of your own.</p>
                ',
            ]
        );

        /*
        ============================================
        CHAPTER 1 — PIECE ACTIVITY & COORDINATION
        ============================================

        Foundation of the course. Every other middlegame concept — tactics,
        pawn structure, attack — depends on understanding what makes a piece
        good or bad, and how pieces amplify each other when they work together.

        LESSON BREAKDOWN:
        Lesson 1 — Good Pieces and Bad Pieces  : What makes a piece active or passive
        Lesson 2 — Outposts                    : Giving pieces a permanent home
        Lesson 3 — Piece Coordination          : How pieces work together as a team
        ============================================
        */

        $chapter1 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 1],
            ['title' => 'Piece Activity & Coordination']
        );

        /*
        ============================================
        LESSON 1 — GOOD PIECES AND BAD PIECES
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Two identical pieces can be completely different in value
        2. Active    — What makes a piece active: range, targets, mobility
        3. Passive   — What makes a piece passive: blocked, on the wrong square, no targets
        4. See it    — Board: good bishop vs bad bishop side by side
        5. The rule  — Improve your worst piece; it is the fastest way to improve your position
        6. See it    — Board: find the worst piece in a position and improve it
        7. Practice  — Board: two candidate moves; one improves the worst piece, one does not
        8. Coach tip — Before every move, ask: which of my pieces is doing the least?
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'good-pieces-and-bad-pieces'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'Good Pieces and Bad Pieces',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Two bishops are worth the same on paper. Two knights are worth the same on paper. But in a real game, two pieces of the same type can be completely different in how much they actually contribute to the position.</p>
                            <br>
                            <p>One bishop might be cutting across the entire board, threatening pieces, controlling key diagonals, participating in attacks. Another bishop — in the same game, on the same side — might be completely blocked by its own pawns, unable to move, doing absolutely nothing. Same piece type, completely different value.</p>
                            <br>
                            <p>The difference is <strong>activity</strong>.</p>
                            <br>
                            <p>An <strong>active piece</strong> has range — it controls many squares, threatens enemy pieces, supports your own pieces, and can reach important areas of the board quickly. An <strong>inactive piece</strong> is cramped, blocked, pointing at nothing useful, contributing nothing to the position.</p>
                            <br>
                            <p>The most common example is the bishop. A bishop is defined by its diagonals — if its own pawns are sitting on those diagonals, blocking every path forward, the bishop becomes what coaches call a <strong>bad bishop</strong>: a piece that looks like it is worth three pawns but plays like it is worth one.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/good-vs-bad-bishop',
                            'instructions' => 'Two bishops — one for each side. White\'s bishop has open diagonals and controls important squares across the board. Black\'s bishop is completely hemmed in by its own pawns, with no useful diagonals available. Both pieces are technically worth the same. But which side has the better bishop? And what could Black do to improve theirs?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>This principle applies to every piece, not just bishops. A knight on the edge of the board reaches fewer squares than a knight in the center. A rook on a closed file contributes far less than a rook on an open file. A queen that has no safe, active square to occupy is just an expensive piece hiding in the corner.</p>
                            <br>
                            <p>One of the most powerful habits in the middlegame is this: <strong>find your worst piece and improve it.</strong> Every position has one piece that is contributing least. Moving that piece to a better square — even if it does not create an immediate threat — is often the single best move available, because it raises the overall quality of your position.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/find-worst-piece',
                            'instructions' => 'Look at White\'s position. One piece is clearly doing much less than the others — it has no targets, controls few squares, and is not participating in the game. Find that piece and move it to a square where it becomes active. There is no immediate threat to calculate here. Just identify the worst piece and improve it.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/improve-worst-piece-choice',
                            'instructions' => 'Two candidate moves are available. One creates a small, temporary threat with an already-active piece. The other activates a passive piece that has been doing nothing for several moves. Which move improves the position more? Think about which one raises the quality of your entire army.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before every move in the middlegame, scan your pieces and ask: which one is doing the least? Moving that piece to a better square — even without an obvious threat — is very often the strongest move on the board. The player with the more active pieces wins, not always immediately, but consistently over time.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — OUTPOSTS
        ============================================

        COACHING STRUCTURE:
        1. Hook      — What if a piece could park on a square and never be chased away?
        2. Definition — An outpost: a square that cannot be attacked by enemy pawns
        3. Why it    — A piece on an outpost is permanently active; opponent cannot remove it with a pawn
        4. See it    — Board: knight planted on an outpost in the center
        5. How to    — How to identify outposts and how to create them
        6. See it    — Board: pawn exchange creates an outpost square; knight occupies it
        7. Practice  — Board: identify the outpost square and place the knight on it
        8. Coach tip — Knights love outposts more than any other piece
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'outposts'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'Outposts',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You learned that active pieces are more powerful than passive ones. Now here is a question: what is the most active a piece can possibly be?</p>
                            <br>
                            <p>The answer is a piece sitting on an <strong>outpost</strong> — a square deep in enemy territory that cannot be attacked by any enemy pawn. A piece on an outpost cannot be chased away with a pawn move. Your opponent must use a more valuable piece to remove it, or accept that it will sit there permanently, controlling the position from the inside.</p>
                            <br>
                            <p>How do you identify an outpost? Look for a square in your opponent\'s half of the board where no enemy pawn can ever reach. If the pawns on the files beside that square have moved past it or been exchanged, the square is an outpost. Any piece you plant there is safe from the cheapest form of attack.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-on-outpost',
                            'instructions' => 'White has a knight planted on d5 — deep in Black\'s half of the board. Look at the pawns around that square. Can any Black pawn attack it? No — the c-pawn and e-pawn are gone, so d5 is a permanent outpost. The knight cannot be chased away with a pawn. It will sit there as long as White wants, controlling key squares and restricting Black\'s position.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Outposts do not always exist at the start of the middlegame — sometimes you have to <strong>create</strong> them.</p>
                            <br>
                            <p>The most common way is through a pawn exchange. When you trade pawns on a file, you remove the pawn that was guarding the outpost square beside it. The square that was previously protected by that pawn is now permanently unguarded — and your piece can move in.</p>
                            <br>
                            <p>Knights benefit from outposts more than any other piece. A bishop on an outpost is powerful, but a bishop can control squares from a distance anyway — its value comes from its diagonals, not from where it physically stands. A knight, by contrast, can only control nearby squares. Planting it on a central outpost maximizes everything about how the knight moves.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/create-outpost',
                            'instructions' => 'White can exchange pawns to create an outpost square for the knight. First, make the pawn exchange that opens up the outpost. Then move the knight to occupy it. Watch how the knight transforms from a passive piece into a permanent fixture in the center of the board.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/outpost-practice',
                            'instructions' => 'An outpost square is available. Find it — look for a square in Black\'s half of the board that no Black pawn can attack — and move the knight there. Then consider: can Black dislodge the knight with any piece? At what cost?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> When you have a knight and your opponent has a bishop, outposts are your best friend. The bishop can attack from a distance, but it cannot remove a knight from an outpost without being traded. A knight on a strong outpost is often worth more than a bishop in those positions — and experienced players will go out of their way to create one.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 3 — PIECE COORDINATION
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Individual active pieces are good; pieces that work together are better
        2. The idea  — Coordination: two or more pieces covering the same area or supporting each other
        3. Battery   — Two rooks on the same file or rank, or rook and queen together
        4. See it    — Board: rook battery doubling on an open file
        5. Bishop    — Two bishops working complementary diagonals
        6. See it    — Board: two bishops controlling the whole board
        7. Knight +  — Knight and bishop covering what the other cannot
        8. See it    — Board: knight and bishop in coordination
        9. Practice  — Board: reorganize pieces so two of them coordinate effectively
        10. Coach tip — One active piece is good; two active pieces pointing at the same target are winning
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'piece-coordination'],
            [
                'chapter_id' => $chapter1->id,
                'title' => 'Piece Coordination',
                'order' => 3,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>An active piece is valuable. Two active pieces working together are more than twice as valuable.</p>
                            <br>
                            <p>This is what <strong>coordination</strong> means: your pieces supporting each other, covering the same area, pointing at the same target, or filling in each other\'s weaknesses. A well-coordinated army of pieces is far more powerful than the same pieces placed without thought for how they interact.</p>
                            <br>
                            <p>Let us look at the most common and powerful forms of coordination.</p>
                            <br>
                            <p><strong>The battery.</strong> Two heavy pieces — rooks, or a rook and a queen — lined up on the same file or rank. When two rooks double on an open file, the pressure they create on that file is enormous. The first rook breaks through; the second supports it. A rook-and-queen battery pointing at the enemy king is one of the most dangerous structures in chess.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/rook-battery',
                            'instructions' => 'White has doubled rooks on the d-file — a battery. Together they control every square on that file and create overwhelming pressure on Black\'s position. Notice how neither rook alone would be as threatening. It is their coordination that makes them dangerous. How would Black defend against this pressure?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>The two bishops.</strong> When both of your bishops are active and unblocked, they cover every square on the board between them — one controls light squares, the other controls dark squares. Together they can restrict the opponent\'s pieces, control key diagonals, and participate in attacks on both sides of the board simultaneously. This is called the <strong>bishop pair</strong>, and it is a recognized advantage in open positions.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/bishop-pair',
                            'instructions' => 'White\'s two bishops are both active and pointing into Black\'s position. Together they cover both colors and restrict Black\'s pieces from settling on safe squares. Look at how many squares the bishop pair controls between them — and notice that Black\'s knight has very few good squares to go to.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Knight and bishop.</strong> A knight and a bishop can coordinate beautifully because they cover what the other cannot. The bishop attacks from a distance along diagonals. The knight jumps to squares the bishop can never reach. When they work together — the knight occupying a square the bishop covers, the bishop controlling squares around where the knight wants to land — the combination is very hard to unravel.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-bishop-coordination',
                            'instructions' => 'The bishop is covering the square the knight wants to jump to, and the knight is threatening squares the bishop cannot attack. Together they are covering an area Black cannot easily defend. Identify which squares each piece is controlling, and see how they complement each other.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/coordination-practice',
                            'instructions' => 'White\'s pieces are active individually, but they are not working together. Reorganize one piece so that it coordinates with another — either by pointing at the same target, covering the same area, or supporting the other piece\'s ambitions. Look for the move that turns two independent pieces into a working team.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> When you are trying to build an attack or win material, do not rely on one piece to do all the work. Chess is a team game. Two pieces aiming at the same target create a problem your opponent has to solve twice. One piece aiming at a target creates a problem they only have to solve once. Always look for ways to bring a second piece into the action.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 2 — PAWN STRUCTURE
        ============================================

        Pawns define the character of the position. They determine which files
        are open or closed, where pieces belong, what the plans are, and what
        the weaknesses are. Understanding pawn structure gives students a
        framework for reading any middlegame position.

        LESSON BREAKDOWN:
        Lesson 1 — Passed Pawns    : The most dangerous pawn type; connect to promotion
        Lesson 2 — Pawn Weaknesses : Isolated, doubled, and backward pawns
        Lesson 3 — Pawn Chains     : Interlocking pawn structures and how to attack them
        ============================================
        */

        $chapter2 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 2],
            ['title' => 'Pawn Structure']
        );

        /*
        ============================================
        LESSON 1 — PASSED PAWNS
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Connect back to promotion: every pawn wants to promote; a passed pawn can
        2. Definition — A passed pawn: no enemy pawn can block or capture it on its path
        3. Why it    — It must be stopped by a piece, which ties that piece down permanently
        4. See it    — Board: passed pawn marching forward; opponent's piece forced to blockade
        5. The rule  — Passed pawns must be pushed
        6. Blockade  — How to stop a passed pawn: place a piece directly in front of it
        7. See it    — Board: blockading a passed pawn with a knight
        8. Practice  — Board: identify the passed pawn and push it to create winning pressure
        9. Coach tip — A passed pawn in the endgame wins games; a passed pawn in the middlegame ties pieces down
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'passed-pawns'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Passed Pawns',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You already know that a pawn reaching the last rank becomes a queen. Most pawns never make it that far — they are blocked or captured long before. But one type of pawn has a genuine chance of making the journey: a <strong>passed pawn</strong>.</p>
                            <br>
                            <p>A passed pawn is a pawn with no enemy pawn in front of it or on either adjacent file. There is nothing on the board that can stop it with a pawn move. The only way to stop a passed pawn is to use a piece — and that piece will be tied down to stopping it for as long as the passed pawn exists.</p>
                            <br>
                            <p>This creates a hidden advantage that goes beyond the pawn itself: the piece blocking the passed pawn is doing nothing else. It cannot attack. It cannot help defend elsewhere. It is a prisoner guarding a pawn. Meanwhile, all your other pieces roam freely.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/passed-pawn-march',
                            'instructions' => 'White has a passed pawn on d5 — no Black pawn can block or capture it by advancing. Watch as it marches forward. Notice how Black has to use a piece to stop it, tying that piece to the d-file while White\'s other pieces move freely. The further the pawn advances, the more dangerous it becomes.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is a famous coaching principle about passed pawns: <strong>passed pawns must be pushed.</strong></p>
                            <br>
                            <p>A passed pawn sitting still does not threaten anything. The further it advances, the more your opponent has to worry about it. Push it forward, support it with pieces, and force your opponent to commit more and more resources to stopping it.</p>
                            <br>
                            <p>The best way to stop a passed pawn — if you are on the defending side — is to place a piece directly in front of it. This is called a <strong>blockade</strong>. A blockading piece freezes the pawn in place and prevents it from advancing. Knights make the best blockaders because they are not weakened by sitting on a square — a knight on d4 blockading a pawn is still active and threatening, unlike a bishop or rook that would rather be on an open line.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/blockade-passed-pawn',
                            'instructions' => 'Black has a dangerous passed pawn advancing up the board. White needs to blockade it — place a piece directly in front of it to stop its march. Which piece should do the blockading? Find the best blockading piece and the best square.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/push-passed-pawn',
                            'instructions' => 'White has a passed pawn that has not been pushed. It is safe to advance. Push the passed pawn forward and see how Black\'s position comes under pressure. Every square it advances is one square closer to promotion.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> In the middlegame, a passed pawn is a long-term weapon — it ties down your opponent\'s pieces and gives you a threat that never goes away. In the endgame, it becomes a direct winning tool. Creating a passed pawn, pushing it forward, and supporting it with your pieces is one of the clearest and most reliable plans in chess.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — PAWN WEAKNESSES
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Not all pawns are equal; some are liabilities, not assets
        2. Isolated  — A pawn with no friendly pawns on adjacent files; cannot be defended by a pawn
        3. See it    — Board: isolated pawn on d4; pieces forced to defend it
        4. Doubled   — Two pawns of the same color on the same file; one blocks the other
        5. See it    — Board: doubled pawns limiting mobility
        6. Backward  — A pawn that cannot advance safely and cannot be protected by another pawn
        7. See it    — Board: backward pawn on an open file, targeted by a rook
        8. Practice  — Board: identify all three types of weak pawns in a position
        9. Coach tip — Attack your opponent's weak pawns; defend your own
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'pawn-weaknesses'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Pawn Weaknesses',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Pawns cannot move backwards. This makes every pawn move permanent — and some pawn structures create weaknesses that cannot be fixed. A weak pawn is not just a pawn at risk of being captured; it is a target that ties down your pieces to defending it, gives your opponent a clear plan, and drains your position for the rest of the game.</p>
                            <br>
                            <p>There are three types of weak pawns worth knowing. Learning to spot them — in your own position and your opponent\'s — is one of the most important positional skills in chess.</p>
                            <br>
                            <p><strong>The isolated pawn.</strong> A pawn is isolated when there are no friendly pawns on either adjacent file. It has no pawn to protect it, which means it must be defended entirely by pieces. Those pieces, instead of being active and attacking, are tied to babysitting a pawn. The isolated pawn becomes a permanent target — your opponent places pieces in front of it and attacks it from every direction.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/isolated-pawn',
                            'instructions' => 'White has an isolated pawn on d4. There are no White pawns on c or e files to support it. Black is targeting it with a rook and a knight. Notice how White\'s pieces must stay near the d4 pawn to defend it — they cannot participate in active play. This is the cost of an isolated pawn.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>The doubled pawn.</strong> Doubled pawns are two pawns of the same color stacked on the same file. This usually happens after a piece capture — a pawn takes toward the center and creates a doubled pawn. The problem is that one pawn directly blocks the other. The front pawn cannot be supported by the one behind it, and both pawns together control fewer squares than two pawns on adjacent files would.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/doubled-pawns',
                            'instructions' => 'Black has doubled pawns on the c-file. One pawn is directly behind the other — they cannot protect each other, and the c-file is partially blocked. Compare the mobility and effectiveness of Black\'s doubled pawns against White\'s healthy pawn structure. What plans does White have against the weakness?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>The backward pawn.</strong> A backward pawn is one that cannot advance safely — the square in front of it is controlled by an enemy pawn — and cannot be supported by a friendly pawn from behind because the pawns on either side have moved ahead of it. It is stuck. Worse, the square directly in front of a backward pawn is often a perfect outpost for the opponent\'s pieces, and if the file in front of the backward pawn is open, a rook on that file will put immediate pressure on it.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/backward-pawn',
                            'instructions' => 'Black has a backward pawn on d6. The d5 square in front of it is controlled by White\'s pawns, so the pawn cannot advance. White has a rook on the open d-file, pressing directly on the backward pawn. Notice how Black is forced into a passive defense. What should White do next to increase the pressure?',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/identify-pawn-weaknesses',
                            'instructions' => 'Look at this position and identify all the pawn weaknesses. Are there any isolated pawns? Any doubled pawns? Any backward pawns? Name each weakness and which side it belongs to. Then decide: whose pawn structure is better, and why?',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Weak pawns give your opponent a plan — and a player with a plan is always more dangerous than a player without one. When you see a weak pawn in your opponent\'s position, make attacking it the center of your strategy. Place a rook on the file in front of it, put a knight on the outpost square it creates, and pile on the pressure. Weak pawns rarely recover.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 3 — PAWN CHAINS
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Pawns protecting each other form a chain; the chain has a head and a base
        2. Definition — A pawn chain: a diagonal line of pawns each protecting the one in front
        3. The base  — The base pawn is the weakest; it cannot be protected by another pawn
        4. See it    — Board: classic pawn chain with base and head identified
        5. Strategy  — Attack the base; the whole chain falls with it
        6. See it    — Board: attacking the base of the chain
        7. Counter   — Advance the head to break the chain before it can be attacked
        8. Practice  — Board: identify the base of the opponent's chain and plan the attack
        9. Coach tip — Every pawn chain has a logical target; find the base and attack it
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'pawn-chains'],
            [
                'chapter_id' => $chapter2->id,
                'title' => 'Pawn Chains',
                'order' => 3,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Sometimes pawns line up diagonally, each one protecting the pawn in front of it. This formation is called a <strong>pawn chain</strong> — and it is one of the most common and important structures in chess.</p>
                            <br>
                            <p>A pawn chain has two ends. The <strong>head</strong> is the most advanced pawn — the one at the front of the chain. The <strong>base</strong> is the pawn at the back — the one closest to your own side of the board. Every other pawn in the chain is protected by the one behind it. But the base pawn has no pawn behind it at all. It is the only pawn in the chain that is not protected by another pawn.</p>
                            <br>
                            <p>This makes the base the weakest point of the entire chain.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/pawn-chain-structure',
                            'instructions' => 'White has a pawn chain running diagonally across the board. Identify the head — the most advanced pawn — and the base — the pawn at the back with no pawn behind it. Every other pawn in the chain is protected. Only the base is vulnerable.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The classic strategy against a pawn chain is simple: <strong>attack the base.</strong></p>
                            <br>
                            <p>If you capture the base pawn, the pawn that was sitting on top of it is now unprotected. You can then capture that one, and the one on top of that, unraveling the entire chain. The whole structure collapses from the bottom up.</p>
                            <br>
                            <p>There is also a counter-strategy available to the side with the chain: <strong>advance the head.</strong> If the head pawn pushes forward before the opponent can organize an attack on the base, it can create new threats, open lines, and change the character of the position before the base comes under attack.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/attack-chain-base',
                            'instructions' => 'Black has a pawn chain. White\'s plan is to attack the base — the pawn at the bottom of the chain with no pawn behind it. Find the move that puts direct pressure on the base pawn. Once the base falls, how does the rest of the chain unravel?',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/pawn-chain-practice',
                            'instructions' => 'White has a pawn chain under pressure. Should White defend the base, advance the head, or do something else entirely? Look at the position, identify what Black is threatening, and find the best response.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> When you face a pawn chain, do not attack it randomly. Find the base — the one pawn the entire chain depends on — and direct all your pressure there. When you have a pawn chain, watch for attacks on your base pawn and either defend it or advance the head before it comes under fire. Pawn chains always have a logical target. Find it.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 3 — TACTICS
        ============================================

        Introduction level only: name, recognize, and see each tactic executed.
        Students are not expected to solve complex multi-step combinations —
        the goal is pattern recognition and awareness that these weapons exist.

        One lesson per tactic, in order of prevalence and ease of understanding:
        Fork → Pin → Skewer → Discovered Attack

        LESSON BREAKDOWN:
        Lesson 1 — The Fork             : One piece attacks two at once
        Lesson 2 — The Pin              : A piece cannot move without exposing something more valuable
        Lesson 3 — The Skewer           : The more valuable piece is forced to move, exposing the one behind
        Lesson 4 — The Discovered Attack: Moving one piece reveals an attack from another
        ============================================
        */

        $chapter3 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 3],
            ['title' => 'Tactics']
        );

        /*
        ============================================
        LESSON 1 — THE FORK
        ============================================

        COACHING STRUCTURE:
        1. Hook      — One piece, two targets: your opponent can only save one
        2. Definition — A fork: a single piece simultaneously attacking two or more enemy pieces
        3. See it    — Board: knight fork attacking king and queen
        4. All pieces — Any piece can fork, but the knight is the best at it; show examples
        5. See it    — Board: pawn fork winning a piece
        6. How to    — How to spot fork opportunities: look for two unguarded pieces on the right pattern
        7. Practice  — Board: find the fork
        8. Coach tip — Always look for knight forks; they are the hardest to see coming
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-fork'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Fork',
                'order' => 1,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Imagine you could attack two of your opponent\'s pieces at the same time with a single move. Your opponent can only move one piece per turn — so no matter what they do, they can only save one of them. You capture the other for free.</p>
                            <br>
                            <p>This is called a <strong>fork</strong> — one piece attacking two or more enemy pieces simultaneously.</p>
                            <br>
                            <p>Any piece can fork, but the <strong>knight</strong> is the best at it. The knight\'s L-shape allows it to attack squares that are not on the same line, diagonal, or file — making its forks very difficult to see coming. A knight on the right square can fork a king and a queen, forcing the king to move and handing you the queen for free.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/knight-fork',
                            'instructions' => 'White\'s knight jumps to a square where it attacks both the Black king and the Black queen at the same time. Black must move the king — it is in check — and White captures the queen on the next move. Watch the fork happen and notice the square the knight lands on.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Knights are not the only pieces that fork. Pawns are actually very effective forkers — a pawn moving forward can attack two pieces on the diagonal squares in front of it simultaneously. Because pawns are worth so little, a pawn fork almost always wins material.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/pawn-fork',
                            'instructions' => 'White advances a pawn to a square where it simultaneously attacks two Black pieces. Black can only save one of them. Find which pawn moves to which square to execute the fork.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>How do you spot fork opportunities? Look for two enemy pieces that are both undefended or poorly placed, and ask: is there a square my piece can move to that attacks both of them at once? For knights specifically, visualize all eight squares the knight can reach from any given position and check whether any of those squares attacks two pieces simultaneously.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/find-the-fork',
                            'instructions' => 'There is a fork available in this position. Find the piece that can fork, find the square it should jump to, and make the move. Remember: a fork attacks two pieces at once with a single move.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before your opponent makes a move, always check: does this move create a fork threat? And after your opponent moves, always ask: does this new position allow me to fork something? Knight forks in particular are easy to miss because the knight\'s movement is so different from every other piece. Train yourself to visualize all eight knight landing squares whenever your knight has a move. One of those squares might be winning the game.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — THE PIN
        ============================================

        COACHING STRUCTURE:
        1. Hook      — A pinned piece is a frozen piece; it cannot move without losing something worse
        2. Definition — A pin: attacking a piece that, if moved, exposes a more valuable piece behind it
        3. Absolute  — An absolute pin: the king is behind the pinned piece (the piece literally cannot move legally)
        4. See it    — Board: bishop pinning a knight to the king
        5. Relative  — A relative pin: a valuable piece (not king) is behind the pinned piece
        6. See it    — Board: rook pinning a knight to the queen
        7. How to    — How to exploit a pin: pile more attackers onto the pinned piece
        8. How to    — How to break a pin: block the pin, capture the pinner, or move the piece behind
        9. Practice  — Board: identify the pin and find the move that exploits it
        10. Coach tip — A pinned piece is a bad piece; a heavily pinned piece is a losing piece
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-pin'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Pin',
                'order' => 2,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Imagine one of your opponent\'s pieces is stuck — it cannot move because moving it would expose something far more valuable behind it. The piece is frozen in place, unable to do its job, serving only as a shield for the piece behind it.</p>
                            <br>
                            <p>This is a <strong>pin</strong>.</p>
                            <br>
                            <p>A pin occurs when you attack a piece that, if it moved, would expose a more valuable piece on the same line behind it. The attacked piece is "pinned" — moving it would be a disaster, so it is forced to stay put.</p>
                            <br>
                            <p>There are two types of pin, and the difference matters.</p>
                            <br>
                            <p>An <strong>absolute pin</strong> is when the king is the piece behind the pinned piece. Because the king can never move into check, the pinned piece literally cannot move — it is illegal. The pinned piece is completely frozen.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/absolute-pin',
                            'instructions' => 'White\'s bishop is attacking Black\'s knight. Behind the knight, on the same diagonal, sits the Black king. The knight cannot move — doing so would leave the king in check, which is illegal. The knight is absolutely pinned. Notice how White can now pile more attackers onto this frozen knight.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>A <strong>relative pin</strong> is when a valuable piece — not the king — is behind the pinned piece. The pinned piece technically can move, but doing so would lose the valuable piece behind it. Moving out of a relative pin is a choice — usually a very bad one.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/relative-pin',
                            'instructions' => 'White\'s rook is attacking Black\'s knight. Behind the knight sits the Black queen. The knight can legally move — but doing so hands over the queen for free. This is a relative pin. Black will almost certainly keep the knight on its square, which means it is effectively frozen just like in an absolute pin.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>How do you exploit a pin? The most effective method is to <strong>pile on attackers</strong>. If a piece is pinned and cannot move, bring more pieces to attack it. Eventually you may be attacking it with more pieces than your opponent can defend it with — and you win material.</p>
                            <br>
                            <p>How do you break a pin? You have three options: <strong>block</strong> the pinning piece by putting a piece between the attacker and the pinned piece, <strong>capture</strong> the piece doing the pinning if it is safe to do so, or <strong>move the piece behind</strong> the pin so there is no longer anything valuable to expose.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/exploit-the-pin',
                            'instructions' => 'Black has a pinned piece. White can exploit it — bring another attacker to bear on the pinned piece and win material. Find the move that adds a second attacker to the pinned piece.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> A pinned piece is a bad piece because it cannot fulfill its purpose — it is stuck defending the piece behind it. When you pin an enemy piece, immediately ask: can I add another attacker? The more you attack a pinned piece, the harder it is to defend. And when you notice one of your own pieces is pinned, do not ignore it — break the pin before your opponent can pile on.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 3 — THE SKEWER
        ============================================

        COACHING STRUCTURE:
        1. Hook      — The reverse pin: the more valuable piece is in front, not behind
        2. Definition — A skewer: attacking a high-value piece, forcing it to move, exposing what is behind it
        3. Compare   — Pin vs skewer: same geometry, different target priority
        4. See it    — Board: rook skewering king to rook behind it
        5. See it    — Board: bishop skewering queen to rook
        6. How to    — Spot skewer opportunities: look for a valuable piece with something behind it on the same line
        7. Practice  — Board: find the skewer
        8. Coach tip — Skewers often win material without the opponent seeing them coming
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-skewer'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Skewer',
                'order' => 3,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You just learned about the pin: you attack a low-value piece that cannot move because something more valuable is behind it.</p>
                            <br>
                            <p>The <strong>skewer</strong> is the reverse. You attack a <strong>high-value</strong> piece directly — and that piece is forced to move, because it cannot afford to be captured. But when it moves, it exposes a less valuable piece hiding behind it. You capture that piece for free.</p>
                            <br>
                            <p>Think of it this way: in a pin, the valuable piece is behind the attacked piece. In a skewer, the valuable piece is in front — and it is the one being attacked.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-skewer',
                            'instructions' => 'White\'s rook attacks the Black king directly. The king must move out of check. But behind the king, on the same file, sits a Black rook. Once the king steps aside, White\'s rook captures the Black rook for free. Watch the skewer happen step by step.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/queen-skewer',
                            'instructions' => 'White\'s bishop attacks the Black queen along a diagonal. Behind the queen on the same diagonal sits a Black rook. The queen must move — losing the queen is unthinkable. But once she steps aside, the bishop captures the rook. Find the skewering move and execute it.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Skewers are particularly effective because the attacked piece has no choice — a queen or king under direct attack must move. There is no option to ignore it the way you might ignore a pin on a less valuable piece. The forced nature of the skewer is what makes it so powerful.</p>
                            <br>
                            <p>To spot a skewer, look for an enemy piece of high value — a king, queen, or rook — that has another of their pieces sitting behind it on the same file, rank, or diagonal. If you can get a long-range piece onto that line, you have a skewer.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/find-the-skewer',
                            'instructions' => 'A skewer is available in this position. Find the high-value piece that can be attacked, identify what is sitting behind it on the same line, and make the move that executes the skewer.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Skewers often win material without the opponent seeing them coming because players naturally focus on protecting their most valuable pieces — they do not always think about what is sitting behind them. When scanning for tactics, always check whether your opponent\'s king, queen, or rooks are lined up on the same file, rank, or diagonal. A line piece aimed at that alignment is a potential skewer.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 4 — THE DISCOVERED ATTACK
        ============================================

        COACHING STRUCTURE:
        1. Hook      — The most surprising tactic: moving a piece reveals an attack from a different piece
        2. Definition — A discovered attack: moving piece A uncovers an attack by piece B
        3. Why it    — The moving piece can make its own threat while the revealed piece attacks; two threats at once
        4. See it    — Board: discovered attack revealing a rook on an open file
        5. Discovered check — When the revealed attack is on the king (most powerful version)
        6. See it    — Board: discovered check with double threat
        7. Double    — When both the moving piece AND the revealed piece give check simultaneously
        8. See it    — Board: double check (only move is to move the king)
        9. Practice  — Board: find the discovered attack
        10. Coach tip — Discovered attacks create two threats at once; your opponent can only answer one
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'the-discovered-attack'],
            [
                'chapter_id' => $chapter3->id,
                'title' => 'The Discovered Attack',
                'order' => 4,
                'xp_reward' => 15,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The discovered attack is one of the most powerful and surprising tactics in chess — and it requires no direct confrontation at all.</p>
                            <br>
                            <p>Here is how it works: you move one piece out of the way, and in doing so, you <strong>uncover</strong> an attack from a different piece that was sitting behind it. The piece you moved was blocking the line of a rook, bishop, or queen. By moving it, you reveal that piece\'s power — and the revealed attack often hits something your opponent was not watching.</p>
                            <br>
                            <p>What makes this so powerful is that the moving piece can also make its own threat on the way. Your opponent now faces <strong>two threats at once</strong> — the one from the piece you moved, and the one from the piece that was revealed. They can only deal with one.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/discovered-attack',
                            'instructions' => 'White\'s knight moves away, uncovering an attack from the rook sitting behind it. As the knight moves, it creates its own threat on a new square. Black faces two attacks and can only deal with one. Watch the sequence and identify which piece delivers the discovered attack and which piece creates the second threat.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The most powerful version of a discovered attack is a <strong>discovered check</strong> — when the revealed piece attacks the king. The opponent must deal with the check immediately, which means they have no time to address whatever the moving piece is threatening. A discovered check almost always wins material.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/discovered-check',
                            'instructions' => 'White moves a piece off a diagonal or file, revealing a check on the Black king from a bishop or rook behind it. As that piece moves, it attacks a second target. Black must get out of check — and then White captures the second target. This is a discovered check winning material.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>There is an even rarer and more devastating version called a <strong>double check</strong>. This happens when both the moving piece and the revealed piece give check at the same time. The king is being attacked by two pieces simultaneously. You cannot block a double check and you cannot capture two pieces at once — the only legal response is to move the king. Double check is the most powerful force in chess.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/double-check',
                            'instructions' => 'White executes a double check — both the moving piece and the revealed piece attack the Black king at the same time. The king cannot escape by blocking or capturing. It must move. Notice how after the double check, the Black king has very few squares to run to.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/find-discovered-attack',
                            'instructions' => 'A discovered attack is available. Find the piece to move, identify what it uncovers behind it, and make sure the moving piece also creates a second threat. Your goal is to create two problems your opponent cannot both solve.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> To find discovered attacks, look for your own pieces that are blocking the line of a long-range piece — a bishop, rook, or queen — behind them. If moving that blocking piece would reveal an attack on something valuable, you have a discovered attack. The more threatening the move of the blocking piece itself, the more powerful the combination. Two threats, one move: the opponent can only answer one.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 4 — ATTACK & DEFENSE
        ============================================

        Culmination of the course. Students now have all the building blocks:
        active pieces, pawn structure knowledge, and tactical awareness.
        This chapter brings them together into the full picture of how to
        launch attacks and how to survive them.

        LESSON BREAKDOWN:
        Lesson 1 — Building an Attack   : Targets, preparation, piece mobilization
        Lesson 2 — Recognizing Danger   : Warning signs that an attack is coming
        Lesson 3 — How to Defend        : Practical defensive techniques
        ============================================
        */

        $chapter4 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 4],
            ['title' => 'Attack & Defense']
        );

        /*
        ============================================
        LESSON 1 — BUILDING AN ATTACK
        ============================================

        COACHING STRUCTURE:
        1. Hook      — An attack is not a random sequence of threats; it is a plan
        2. Target    — Every attack needs a target: the king, a weak pawn, an undefended piece
        3. Prepare   — Before attacking, make sure your pieces are ready (connects to coordination)
        4. See it    — Board: a premature attack that fails because pieces are not coordinated
        5. Principle — Only attack when your pieces outnumber the defenders
        6. See it    — Board: counting attackers vs defenders before committing
        7. Open      — Open lines toward the target: files, diagonals, ranks
        8. See it    — Board: opening a file toward the enemy king
        9. Practice  — Board: build up the attack before striking
        10. Coach tip — Preparation beats impulse; always count before you attack
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'building-an-attack'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Building an Attack',
                'order' => 1,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>An attack is not a series of random threats. It is a plan — and like any plan, it requires preparation before it can succeed.</p>
                            <br>
                            <p>The biggest mistake beginners make when attacking is starting too early. They see a slightly exposed enemy king and immediately rush every piece toward it, making threats before those pieces are coordinated or supported. The opponent defends calmly, the attack fizzles, and the attacker is left with misplaced pieces and no plan.</p>
                            <br>
                            <p>Every successful attack has three ingredients:</p>
                            <br>
                            <p><strong>1. A clear target.</strong> What are you attacking? The enemy king is the ultimate target. But if the king is well-defended, look for a weak pawn, an undefended piece, or a square you can occupy. You need a specific target before anything else.</p>
                            <br>
                            <p><strong>2. More attackers than defenders.</strong> Before committing to an attack, count how many pieces you have aimed at the target and how many pieces your opponent has defending it. If you have fewer attackers than they have defenders, your attack will fail — they can simply trade off your attackers one by one. Only launch the attack when the numbers are in your favor.</p>
                            <br>
                            <p><strong>3. Open lines to the target.</strong> Your pieces need paths to reach the target. Open files for rooks, open diagonals for bishops, clear routes for the queen. An attack with no open lines runs into a wall before it begins.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/premature-attack',
                            'instructions' => 'White launches an attack too early — pieces are not coordinated and the attack is not well-prepared. Watch how Black defends simply and the attack collapses. After the sequence, count how many White attackers were aimed at the target versus how many Black defenders were available. The numbers tell the story.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/count-attackers-defenders',
                            'instructions' => 'Before attacking, count. How many White pieces are aimed at Black\'s king? How many Black pieces are defending it? If the attacker does not outnumber the defender, the attack is not ready. Look at this position and decide: is White ready to attack, or does White need to bring another piece into the attack first?',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/open-the-lines',
                            'instructions' => 'White has more attackers than Black has defenders — but the lines to the king are closed. Find the move that opens a file or diagonal toward Black\'s king, giving the attack a path to strike. Once the lines are open, the attack becomes unstoppable.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/build-the-attack',
                            'instructions' => 'The position is not quite ready to attack. White needs one more preparatory move — bring a piece to a better square, open a line, or improve coordination. Find the move that completes the preparation, then follow up with the attack.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Before every attacking move, ask three questions: do I have a clear target, do I have more attackers than they have defenders, and are my lines to the target open? If any answer is no, improve the position first. One more preparatory move often makes the difference between an attack that wins and one that falls apart.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — RECOGNIZING DANGER
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Defense begins before the attack arrives; recognizing it early changes everything
        2. Signs     — Warning signs that an attack is coming: pieces aimed at your king, open files, pawn advances
        3. See it    — Board: early warning signs in a position; identify them before the attack lands
        4. The king  — An uncastled king in the center is the most common danger sign
        5. See it    — Board: attack exploiting uncastled king
        6. Weak      — Weaknesses around the castled king: pawn moves that open lines to the king
        7. See it    — Board: h3 or g3 pawn weakness exploited
        8. Practice  — Board: look at a position and identify the three biggest danger signs
        9. Coach tip — Every move your opponent makes is a message; learn to read it
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'recognizing-danger'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'Recognizing Danger',
                'order' => 2,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Good defense does not start when the attack arrives. It starts when you first notice it building — before any piece has been sacrificed, before any threat has been made explicitly. The player who recognizes danger early has time to prepare. The player who recognizes it late is already in trouble.</p>
                            <br>
                            <p>Here are the warning signs to watch for.</p>
                            <br>
                            <p><strong>Pieces pointing at your king.</strong> When your opponent moves a bishop onto a diagonal aimed at your king, brings a rook to an open file in front of your king, or advances the queen toward your castled position, they are not doing it by accident. Every piece they orient toward your king is a move in the preparation of an attack. Count how many pieces are aimed at your king after each of your opponent\'s moves.</p>
                            <br>
                            <p><strong>Pawn advances toward your king.</strong> When your opponent starts pushing pawns toward your castled king, they are trying to open lines. A pawn advance on the h-file against a king castled kingside is almost always the beginning of a pawn storm. Do not wait for the pawn to arrive — respond early.</p>
                            <br>
                            <p><strong>Open or half-open files near your king.</strong> An open file gives rooks a direct highway to your king. If a file beside your king opens up — through an exchange or a pawn advance — and your opponent has a rook or queen available, that is a serious danger sign.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/early-warning-signs',
                            'instructions' => 'Look at this position from Black\'s perspective. How many of White\'s pieces are aimed at or near the Black king? Is any file near the king open or about to open? Are there any pawns advancing toward Black\'s castled position? Identify every warning sign before the attack has been launched.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>The most dangerous position is a king that never castled, still sitting in the center. You learned in Opening Principles that an uncastled king is a target. In the middlegame, that target becomes a bullseye — every open file, every piece, every pawn advance from the opponent can be directed at the center where the king is sitting.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/uncastled-king-attack',
                            'instructions' => 'Black\'s king never castled and is still in the center. White opens the e-file and aims every piece at the king. Watch how quickly the attack builds when the king has nowhere to hide. This is why castling early matters.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Even a castled king can be in danger if the pawns in front of it have been weakened. Moving the h-pawn, g-pawn, or f-pawn in front of your castled king — without a very good reason — creates holes that your opponent\'s pieces can use to infiltrate. A bishop aimed at f7, a rook on the h-file with no h-pawn blocking it, a queen approaching on the diagonal — all of these become threats the moment the pawn structure around the king is compromised.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/pawn-weakness-king',
                            'instructions' => 'Black moved the h-pawn earlier, creating a hole next to the castled king. White is now exploiting that weakness. Watch how the open h-file and the weakened pawn structure give White a direct route to the king. This is the long-term cost of unnecessary pawn moves in front of a castled king.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/spot-the-danger',
                            'instructions' => 'Assess this position as if you were playing Black. What are the three biggest danger signs in your position? Which of White\'s pieces are aimed at your king, which files are open or about to open, and are there any weaknesses in your pawn structure? Name every warning sign you can find.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Every move your opponent makes is a message. Most of the time it is saying: I am preparing this plan, I am targeting this square, I am building toward this attack. If you read those messages early, you have time to respond. If you ignore them and focus only on your own plans, you will be blindsided. After every opponent move, ask: what is my opponent threatening now, and what are they preparing for next?</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 3 — HOW TO DEFEND
        ============================================

        COACHING STRUCTURE:
        1. Hook      — Defense is not just surviving; it is preparing to counter-attack
        2. Principle — Defend actively, not passively: active defense creates counter-threats
        3. See it    — Board: passive defense that loses vs active defense that holds
        4. Technique — Exchange attackers: trade off the pieces driving the attack
        5. See it    — Board: trading the key attacking piece defuses the attack
        6. Technique — Counterattack: instead of only defending, create your own threat
        7. See it    — Board: ignoring the attack and winning by counter-attacking faster
        8. Technique — King safety: move the king away from danger before the attack lands
        9. Practice  — Board: position under attack; find the best defensive move
        10. Coach tip — The best defense is often a good offense
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'how-to-defend'],
            [
                'chapter_id' => $chapter4->id,
                'title' => 'How to Defend',
                'order' => 3,
                'xp_reward' => 20,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Defense has a reputation for being passive and boring — just sitting there, blocking attacks, waiting to lose. Good defense is none of those things.</p>
                            <br>
                            <p>The best defenders in chess do not just react to threats. They find ways to defend that simultaneously create problems for the attacker. Active defense does not just stop the attack — it turns the tide. Passive defense just delays the inevitable.</p>
                            <br>
                            <p>Here are the three most important defensive techniques.</p>
                            <br>
                            <p><strong>Exchange the key attacker.</strong> Every attack has a piece driving it — often a queen, a bishop on a powerful diagonal, or a rook on an open file. If you can trade off that piece, the attack loses its most dangerous weapon. Look for opportunities to exchange the opponent\'s most active attacking piece, even if it means giving up your own active piece in return. Defusing an attack is worth a lot.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/exchange-key-attacker',
                            'instructions' => 'White is attacking with a bishop on a dangerous diagonal and a queen bearing down on the king. Black can trade off the key attacker — find which piece is driving the attack and execute the exchange. After the trade, the attack is much less threatening.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Counterattack.</strong> Sometimes the best answer to an attack is not to defend at all — it is to create a bigger threat of your own. If your counter-threat is fast enough and dangerous enough, your opponent has to abandon their attack to deal with what you are threatening. This works best when your counter-threat involves the opponent\'s king, because a king threat always takes priority.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/counterattack',
                            'instructions' => 'Black is under attack on the kingside. Instead of defending passively, Black can launch a counter-attack on the queenside that is faster and more dangerous. Find the counter-threat that forces White to stop their own attack and defend. The best defense is sometimes the fastest offense.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Move the king.</strong> When an attack is targeting your king\'s current position, sometimes the cleanest solution is to simply move the king somewhere safer. Players often feel reluctant to move their king in the middlegame — it feels exposed and dangerous. But a king that sidesteps an attack and finds a safe square is better than a king that stays put and gets mated. Know when to run.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/king-sidestep',
                            'instructions' => 'White\'s attack is aimed at Black\'s current king position. Blocking and exchanging are not enough. But there is a safe square the king can step to, away from all the danger. Find the king move that gets out of the attack\'s path — and notice how the attack immediately loses its target.',
                        ]
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/defensive-challenge',
                            'instructions' => 'Your position is under pressure. You have three defensive options available — you can exchange the key attacker, launch a counter-attack, or move the king to safety. Evaluate each option. Which one gives you the best chances? Make your choice and see what happens.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> The best defense is often a good offense. Passive defense — moving a piece back, blocking with another piece, hoping the attack stops — almost never works against a well-prepared attack. Active defense looks for ways to make life difficult for the attacker at the same time. Exchange the dangerous piece, create a counter-threat, or find the king a safer square. Defense is not surrender — it is chess played from the other direction.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        CHAPTER 5 — PUTTING IT TOGETHER
        ============================================

        LESSON BREAKDOWN:
        Lesson 1 — A Model Middlegame  : All concepts applied in one annotated game
        Lesson 2 — Course Summary      : Full recap and what comes next
        ============================================
        */

        $chapter5 = Chapter::updateOrCreate(
            ['course_id' => $course->id, 'order' => 5],
            ['title' => 'Putting It Together']
        );

        /*
        ============================================
        LESSON 1 — A MODEL MIDDLEGAME
        ============================================

        COACHING STRUCTURE:
        1. Hook      — All the pieces (piece activity, pawn structure, tactics, attack) in one game
        2. Phase 1   — Opening into middlegame: how piece activity is established
        3. Phase 2   — Pawn structure defines the plan: where to attack, what to target
        4. Phase 3   — Tactic appears: fork or pin that wins material, arising naturally from the position
        5. Phase 4   — Attack builds: more attackers than defenders, lines opened
        6. Conclusion — How it all connects; none of these ideas existed in isolation
        7. Practice  — Board: play from the middlegame position, applying the principles
        8. Coach tip  — Every middlegame idea you learned is a tool; great players use them together
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'a-model-middlegame'],
            [
                'chapter_id' => $chapter5->id,
                'title' => 'A Model Middlegame',
                'order' => 1,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You have learned a lot. Piece activity, outposts, coordination, pawn structure, four tactical weapons, building an attack, recognizing danger, how to defend. Each idea on its own is valuable. But the real test of understanding is seeing them work together in a single game.</p>
                            <br>
                            <p>What follows is an annotated middlegame — not a famous game to memorize, but a model game that deliberately shows each concept from this course appearing in sequence, naturally, as the position develops. Watch for each idea as it emerges.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/model-middlegame-annotated',
                            'instructions' => 'Follow this game move by move and read each annotation carefully. You will see: a piece being improved to a better square, a pawn structure weakness being identified and targeted, a knight outpost being created and occupied, a pin being established and exploited, and an attack being built with more attackers than defenders. Every concept from this course appears here. Try to spot each one before reading the annotation.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>None of these ideas appeared in isolation. The knight could reach the outpost because of the pawn structure. The pin arose because the pieces were well-coordinated. The attack succeeded because the pieces were active and the lines were open. And the defender lost because they recognized the danger too late and defended passively.</p>
                            <br>
                            <p>That is the middlegame. Not a checklist — a connected system of ideas, each one reinforcing the others.</p>
                        '
                    ],
                    [
                        'type' => 'board',
                        'task' => [
                            'lichessUrl' => 'https://lichess.org/study/embed/PLACEHOLDER/model-middlegame-practice',
                            'instructions' => 'The position is set to a critical moment from the model game — just before the decisive combination. Now it is your turn to find the winning idea. Use everything you have learned: look for the worst piece to improve, the pawn weakness to target, the tactic to execute, or the attack to launch. There is a best move. Find it.',
                        ]
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p><strong>Coach\'s tip:</strong> Every idea in this course is a tool. A craftsman with one tool can do one job. A craftsman with many tools, and the skill to choose the right one, can build anything. That is where you are headed. The more positions you play and study, the more naturally these tools will come to you — not as rules you recall, but as patterns you recognize.</p>
                        '
                    ],
                ]
            ]
        );

        /*
        ============================================
        LESSON 2 — COURSE SUMMARY
        ============================================

        COACHING STRUCTURE:
        1. Opening    — Acknowledge the progression across three courses
        2. Chapter 1  — Piece activity: good vs bad pieces, outposts, coordination
        3. Chapter 2  — Pawn structure: passed pawns, weaknesses, chains
        4. Chapter 3  — Tactics: fork, pin, skewer, discovered attack
        5. Chapter 4  — Attack & defense: build attacks, recognize danger, defend actively
        6. The thread — All of it connects back to one idea: active pieces in good positions create opportunities
        7. What's next — Specific next steps: tactics training, endgame, named openings, playing games
        8. Close      — Short, confident send-off
        ============================================
        */

        Lesson::updateOrCreate(
            ['slug' => 'middlegame-summary'],
            [
                'chapter_id' => $chapter5->id,
                'title' => 'Course Summary',
                'order' => 2,
                'xp_reward' => 25,
                'content_blocks' => [
                    [
                        'type' => 'text',
                        'content' => '
                            <p>You started with the rules of chess. Then you learned how to open a game with purpose. Now you have learned how to play the middlegame — the most complex and rewarding phase of chess.</p>
                            <br>
                            <p>That is a complete foundation. Let us look back at what you built.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 1 — Piece Activity & Coordination</h3>
                            <br>
                            <ul>
                                <li><strong>Good vs bad pieces.</strong> An active piece controls important squares and participates in the game. A passive piece is blocked, cramped, or pointing at nothing. Always find your worst piece and improve it.</li>
                                <li><strong>Outposts.</strong> A square in enemy territory that no enemy pawn can attack. A piece on an outpost is permanently active — the opponent must use a more valuable piece to remove it.</li>
                                <li><strong>Coordination.</strong> Pieces working together — pointing at the same target, covering the same area, filling in each other\'s weaknesses — are more powerful than the same pieces working independently.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 2 — Pawn Structure</h3>
                            <br>
                            <ul>
                                <li><strong>Passed pawns.</strong> A pawn with no enemy pawn to stop it. Must be pushed. Forces the opponent to use a piece as a blockader. Threatens promotion and ties down the defense.</li>
                                <li><strong>Pawn weaknesses.</strong> Isolated pawns have no pawn defenders. Doubled pawns block each other. Backward pawns are stuck and easy to target. Attacking your opponent\'s weak pawns gives you a plan.</li>
                                <li><strong>Pawn chains.</strong> A diagonal pawn formation where each pawn protects the one in front. The base is the weakest point — attack it. If you have the chain, watch the base and consider advancing the head.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 3 — Tactics</h3>
                            <br>
                            <ul>
                                <li><strong>The fork.</strong> One piece attacks two targets at once. Your opponent can save only one.</li>
                                <li><strong>The pin.</strong> Attacking a piece that cannot move without exposing something more valuable behind it. Absolute pins freeze a piece completely. Relative pins make moving very costly.</li>
                                <li><strong>The skewer.</strong> Attacking a high-value piece, forcing it to move, and capturing what it was hiding behind it. The reverse of the pin.</li>
                                <li><strong>The discovered attack.</strong> Moving one piece reveals an attack from another. Creates two threats at once. A discovered check is even more powerful — and a double check is almost impossible to survive.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>Chapter 4 — Attack & Defense</h3>
                            <br>
                            <ul>
                                <li><strong>Building an attack.</strong> Find a clear target. Make sure you have more attackers than they have defenders. Open lines to the target. Prepare before you strike.</li>
                                <li><strong>Recognizing danger.</strong> Watch for pieces aimed at your king, pawn advances toward your castled position, and open files near your king. The earlier you spot an attack building, the more time you have to prevent it.</li>
                                <li><strong>How to defend.</strong> Exchange the key attacker, launch a counter-attack, or move the king to safety. Active defense creates counter-threats. Passive defense rarely works against a prepared attack.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>The Thread Running Through All of It</h3>
                            <br>
                            <p>Every idea in this course connects back to one principle: <strong>active pieces in good positions create opportunities</strong>.</p>
                            <br>
                            <p>Piece activity means your pieces are doing something useful. Good positions means the pawn structure supports your pieces and restricts your opponent\'s. Opportunities means tactics appear, attacks become possible, and defense becomes manageable — not because you forced them artificially, but because you built the right kind of position.</p>
                            <br>
                            <p>This is what the middlegame is. Not a series of separate skills — one connected approach to chess, applied from every angle.</p>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <h3>What Comes Next</h3>
                            <br>
                            <ul>
                                <li><strong>Tactics training.</strong> You have been introduced to four tactical patterns. The next step is to practice them in large numbers — solving puzzles until you recognize each pattern instantly, without having to think. Tactics are the fastest way to improve at chess, and the more patterns you know, the more opportunities you will spot in your games.</li><br>
                                <li><strong>The endgame.</strong> You have learned the opening and the middlegame. The endgame is the final phase — king and pawn endgames, rook endgames, and the technique of converting a small advantage into a win. Understanding endgames changes how you play the whole game, because you start making decisions based on what kind of endgame you want to reach.</li><br>
                                <li><strong>Deeper opening study.</strong> With your understanding of middlegame principles, you are ready to study specific openings at a deeper level — not just the first five moves, but understanding the plans and structures each opening leads to in the middlegame.</li><br>
                                <li><strong>Play more games.</strong> Every game you play — win or lose — is practice. After each game, ask yourself: did my pieces become active? Did I identify the pawn weaknesses? Did I spot the tactics? Playing and reflecting is how everything you have learned becomes instinct.</li>
                            </ul>
                        '
                    ],
                    [
                        'type' => 'text',
                        'content' => '
                            <p>Three courses. Rules, opening, middlegame.</p>
                            <br>
                            <p>You now understand how chess is played at every level — from the first move to the critical moments in the heat of the battle.</p>
                            <br>
                            <p>The board is yours. Use everything you know.</p>
                        '
                    ],
                ]
            ]
        );
    }
}