<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach($users as $user)
    <url>
        <loc>{{ $baseUrl }}/user/{{ $user->id }}</loc>
        <lastmod>{{ $user->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    @foreach($tournaments as $tournament)
    <url>
        <loc>{{ $baseUrl }}/events/{{ $tournament->id }}</loc>
        <lastmod>{{ $tournament->updated_at->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @foreach($arenas as $arena)
    <url>
        <loc>{{ $baseUrl }}/events/{{ $arena->id }}/arena</loc>
        <lastmod>{{ $arena->updated_at->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach

    @if(isset($pcapMatches))
    @foreach($pcapMatches as $match)
    <url>
        <loc>{{ $baseUrl }}/pcap/game/{{ $match->id }}/1/rapid</loc>
        <lastmod>{{ $match->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ $baseUrl }}/pcap/game/{{ $match->id }}/1/blitz</loc>
        <lastmod>{{ $match->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @endif
</urlset>
