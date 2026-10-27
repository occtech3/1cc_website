<?php
/**
 * Page template for the "Ministries" page (slug: ministries)
 *
 * Generated from src/pages/ministries.html by build.py; edit the source and rebuild.
 *
 * @package One_Christian_Church
 */

get_header();
?>
<main id="main">
<section class="hero hero-page">
  <div class="media"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'kids', true ); ?></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="wrap hero-copy">
    <p class="kicker">Ministries</p>
    <h1>A place for <span class="grad-text">every age.</span></h1>
    <p class="lead">From our littlest ones to our seniors, there's a group where you can grow, serve and belong.</p>
  </div>
  <button class="video-toggle" type="button" data-video-toggle aria-pressed="false">Pause video</button>
  <svg class="wave" style="--wave:var(--cream)" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true"><path d="M0 46c120-26 240-38 360-24s240 52 360 52 240-40 360-50 240 6 360 26v40H0z"/></svg>
</section>
<nav class="subnav" aria-label="Ministries">
  <div class="wrap"><a href="#kids">Kids</a><a href="#youth">Youth</a><a href="#adults">Adults</a><a href="#house-church">House Church</a><a href="#griefshare">GriefShare</a></div>
</nav>

<section class="section" id="kids">
  <div class="wrap">
    <div class="split">
      <div class="reveal"><p class="kicker">Kids &amp; youth</p><h2>Helping kids <span class="grad-electric-text">follow Jesus.</span></h2></div>
      <p class="reveal" style="font-size:1.15rem">Our mission is to help kids follow Jesus, love God and love people. Every age group has a space built just for them, with leaders who care.</p>
    </div>
    <div class="age-grid">
      <div class="age t-yellow reveal"><span class="chip">Infants &ndash; Kindergarten</span><h3>First Steps</h3><p>Sharing the love of Christ and teaching the gospel to our little ones with care.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Sundays at 10am</span></div>
      <div class="age t-pink reveal"><span class="chip">1st &ndash; 5th grade</span><h3>OCC Kids</h3><p>Teaching our kids the gospel, and how to love and walk with Jesus.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Sundays at 10am</span></div>
      <div class="age t-violet reveal" id="youth"><span class="chip">6th &ndash; 12th grade</span><h3>OCC Youth</h3><p>Worship, life groups and games. Snacks provided!</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Wednesdays 6:30&ndash;8pm</span></div>
    </div>
    <p class="note reveal" style="margin-top:1.6rem">Questions or cancellations? Contact Youth Directors Devon or Jordyn Matthews through the <a href="<?php echo esc_url( occ_url( 'contact', '' ) ); ?>">church office</a>.</p>
  </div>
</section>

<section class="band" style="min-height:70svh">
  <div class="media"><div data-parallax="0.12" style="position:absolute;inset:0"><div class="aurora" aria-hidden="true"><span></span><span></span><span></span><span></span></div><?php occ_video( 'youth', false ); ?></div></div>
  <div class="tint tint-color"></div><div class="tint tint-dark"></div>
  <div class="wrap">
    <h2 class="reveal">Wednesday<br>nights are <span class="script" style="font-size:1.05em">loud.</span></h2>
    <p class="reveal">OCC Youth meets every Wednesday from 6:30 to 8pm for worship, life groups and games. Bring a friend.</p>
  </div>
</section>

<section class="section dark" id="adults">
  <div class="wrap">
    <div class="section-head"><div class="reveal"><p class="kicker">Adult ministries</p><h2>Grow together.<br><span class="grad-text">Never alone.</span></h2></div></div>
    <div class="cards">
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22c4 0 7-3 7-7 0-5-4-7-5-11-2 2-3 4-3 6-1-1-2-2-2-4-2 2-4 5-4 9 0 4 3 7 7 7z"/></svg></span><h3>Men's Fire</h3><p>Fellowship, scripture and worship for men. Mark Spencer speaks in the Prayer Room.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> 3rd Thursday monthly &middot; 6:30pm</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg></span><h3>Women's Fire</h3><p>A weekly women's Bible study in the Prayer Room. Contact Becky Smart to learn more.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Mondays &middot; 6:30pm</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5a2 2 0 0 1 2-2h13v16H6a2 2 0 0 0-2 2z"/><path d="M4 21V5M9 8h6"/></svg></span><h3>Beloved Warriors</h3><p>Women's Bible studies on Thursday and Friday mornings during the school year. <a href="https://www.belovedwarriors.org/" target="_blank" rel="noopener" style="color:var(--yellow)">belovedwarriors.org</a></p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Thu &amp; Fri mornings</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14.2a5 5 0 0 1 5.5 4.8"/></svg></span><h3>Women's Ministry</h3><p>Lunches out, Bible studies and friendships that go deeper than Sunday morning.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg> Throughout the year</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9h13v5a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 11h1.5a2.5 2.5 0 0 1 0 5H17"/><path d="M8 3v3M12 3v3"/></svg></span><h3>Men's Breakfast</h3><p>Jesus, <em>and</em> breakfast? Join the men of the church for a meal and fellowship at the church.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg> At the church</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8z"/></svg></span><h3>Crafters</h3><p>Crafts and fellowship, with a snack and a devotional. Bring a project or start a new one.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Tuesdays &middot; 6:30pm</span></div>
      <div class="card reveal"><span class="ic"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8.5 14.5a4.5 4.5 0 0 0 7 0"/><path d="M9 9.5h.01M15 9.5h.01"/></svg></span><h3>The Incredibles</h3><p>A luncheon for our seniors, ages 55 and older, with good food and great company.</p><span class="when"><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg> Every other month</span></div>
    </div>
  </div>
</section>

<section class="section" id="house-church">
  <div class="wrap split">
    <div class="reveal from-left"><p class="kicker">House Church</p><h2>Church around<br>the <span class="grad-text">kitchen table.</span></h2>
      <p>Small groups gathering in homes for Bible study, prayer and life together.</p>
      <p class="placeholder-note">Placeholder: the current site has no details for House Church yet. Send the meeting days, locations and a short description to fill in here.</p>
    </div>
    <div class="visit-media reveal from-right">
      <span class="blob b1"></span><span class="blob b2"></span>
      <div class="frame"><div class="media"><?php occ_video( 'students', false ); ?></div></div>
    </div>
  </div>
</section>

<section class="section paper" id="griefshare">
  <div class="wrap split">
    <div class="visit-media reveal from-left">
      <span class="blob b1" style="background:var(--grad-ocean)"></span><span class="blob b2" style="background:var(--grad-electric)"></span>
      <div class="frame"><div class="media"><?php occ_video( 'walk', false ); ?></div></div>
    </div>
    <div class="reveal from-right">
      <p class="kicker">Support ministry</p>
      <h2>GriefShare</h2>
      <p style="font-size:1.1rem">A 13-week support group for anyone grieving the death of someone close. You don't have to walk through grief alone.</p>
      <ul class="facts">
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg> Saturdays at 10am</li>
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg> Prayer Room</li>
        <li><svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg> Leader: Jan Nordsiek, <a href="mailto:janniegay@comcast.net">janniegay@comcast.net</a></li>
      </ul>
      <div class="btn-row"><a class="btn btn-sun" href="https://www.griefshare.org/events/264400" target="_blank" rel="noopener">Register for GriefShare <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div>
      <p class="placeholder-note">The current site lists last winter's dates. Send the next session's start date to show it here.</p>
    </div>
  </div>
</section>

</main>
<?php
get_footer();
