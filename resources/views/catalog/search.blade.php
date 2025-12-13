@props(['catalog' => []])

<div x-data="catalogSearch()" class="p-6 space-y-6">

  {{-- Search box --}}
  <div class="flex items-center">
    <input
      type="search"
      placeholder="Search brands, categories… (try: channle, louie viton, ysl belt)"
      x-model="q"
      class="w-full rounded-lg border border-white/10 bg-surface px-4 py-2 text-sm outline-none focus:border-brand focus:ring-brand"
      aria-label="Search catalog"
    />
  </div>

  {{-- Results grid (the partial uses x-show="match(...)") --}}
  <div class="mm-scroll grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 overflow-y-auto max-h-[24rem] pr-2">
    @include('partials.grouped-catalog', ['catalog' => $catalog])
  </div>
</div>

<script>
function catalogSearch() {
  // --- helpers ---
  const norm = (s='') =>
    s.toString()
     .normalize('NFD')                // split accents
     .replace(/[\u0300-\u036f]/g,'') // drop accents
     .toLowerCase()
     .replace(/[^a-z0-9\s]/g,' ')    // keep letters/numbers
     .replace(/\s+/g,' ')            // collapse spaces
     .trim();

  // common brand synonyms / misspellings → canonical tokens added to the query
  const canon = (q) => {
    const n = ' ' + norm(q) + ' ';
    const adds = [];

    // brand synonyms/misspellings
    if (/\b(chann?e?l|chanell|channle)\b/.test(n)) adds.push('chanel');
    if (/\b(lou[ui]e?\s+v[ui]t+on|lv|louis\s*viton)\b/.test(n)) adds.push('louis vuitton','lv');
    if (/\b(herm[eè]s|hremes|ermes)\b/.test(n)) adds.push('hermes');
    if (/\b(saint\s*laurent|st\s*laurent|ysl)\b/.test(n)) adds.push('saint laurent','ysl');
    if (/\b(bulgari|bvlgari)\b/.test(n)) adds.push('bvlgari','bulgari');
    if (/\b(off\s*white|offwhite)\b/.test(n)) adds.push('off white');
    if (/\b(dolce\s*&?\s*gabbana|dg)\b/.test(n)) adds.push('dolce gabbana','dg');
    if (/\b(chrome\s*hearts|chromehearts)\b/.test(n)) adds.push('chrome hearts');
    if (/\b(loro\s*piana|lp)\b/.test(n)) adds.push('loro piana');
    if (/\b(gi?venchy|givency)\b/.test(n)) adds.push('givenchy');
    if (/\b(ce?lin[eé])\b/.test(n)) adds.push('celine');
    if (/\b(mi+u\s*mi+u)\b/.test(n)) adds.push('miu miu');
    if (/\b(versac[eè])\b/.test(n)) adds.push('versace');
    if (/\b(pradaa?)\b/.test(n)) adds.push('prada');
    if (/\b(valenteno|valentino)\b/.test(n)) adds.push('valentino');

    return (norm(q) + ' ' + adds.join(' ')).trim();
  };

  // Damerau–Levenshtein distance (small + fast)
  function editDistance(a, b) {
    a = norm(a); b = norm(b);
    const m = a.length, n = b.length;
    if (!m) return n; if (!n) return m;
    const dp = Array.from({length: m+1}, () => new Array(n+1).fill(0));
    for (let i=0;i<=m;i++) dp[i][0]=i;
    for (let j=0;j<=n;j++) dp[0][j]=j;

    for (let i=1;i<=m;i++) {
      for (let j=1;j<=n;j++) {
        const cost = a[i-1] === b[j-1] ? 0 : 1;
        dp[i][j] = Math.min(
          dp[i-1][j] + 1,      // deletion
          dp[i][j-1] + 1,      // insertion
          dp[i-1][j-1] + cost  // substitution
        );
        // transposition
        if (i>1 && j>1 && a[i-1]===b[j-2] && a[i-2]===b[j-1]) {
          dp[i][j] = Math.min(dp[i][j], dp[i-2][j-2] + cost);
        }
      }
    }
    return dp[m][n];
  }

  // fuzzy contains: true if exact contains OR small edit distance on words
  const fuzzyContains = (hay, needle) => {
    if (!needle) return true;
    const H = norm(hay); const N = norm(needle);
    if (!H) return false;
    if (H.includes(N)) return true;

    // split into terms; allow each needle term to be:
    // - substring of a hay term OR
    // - edit distance <= threshold
    const hTerms = H.split(' ');
    const nTerms = N.split(' ');

    return nTerms.every(nt => {
      const thr = nt.length <= 4 ? 1 : (nt.length <= 7 ? 2 : 3);
      return hTerms.some(ht =>
        ht.includes(nt) || editDistance(ht, nt) <= thr
      );
    });
  };

  return {
    q: '',
    // Main matcher used by x-show
    match(text) {
      const query = canon(this.q);           // expand synonyms/misspellings
      if (!query) return true;
      return fuzzyContains(text, query);
    }
  }
}
</script>
