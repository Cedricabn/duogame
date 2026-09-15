<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#12132a">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Duogame — jeux à deux voix</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,450;0,9..144,600;1,9..144,450&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  :root{
    --indigo:#5B6FE8;
    --violet:#8B5CF6;
    --corail:#FF8966;
    --bg:#F5F4FB;
    --surface:#FFFFFF;
    --ink:#1A1B2E;
    --ink-soft:#4B4D6B;
    --cream:#1A1B2E;
    --cream-dim:#5A5C82;
    --rose:#FF8966;
    --rose-dim:#D9531E;
    --gold:#5B6FE8;
    --gold-dim:#4356C9;
    --thread:#8B5CF6;
    --line:rgba(26,27,46,0.12);
    --radius:22px;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{ -webkit-text-size-adjust:100%; }
  body{
    background:
      radial-gradient(ellipse 70% 50% at 10% -8%, rgba(139,92,246,0.14) 0%, transparent 55%),
      radial-gradient(ellipse 60% 45% at 108% 10%, rgba(255,137,102,0.14) 0%, transparent 50%),
      var(--bg);
    background-color:var(--bg);
    color:var(--ink);
    font-family:'Inter', sans-serif;
    min-height:100vh;
    min-height:100svh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px 18px;
    padding-top:max(32px, env(safe-area-inset-top));
    padding-bottom:max(32px, env(safe-area-inset-bottom));
    padding-left:max(18px, env(safe-area-inset-left));
    padding-right:max(18px, env(safe-area-inset-right));
    line-height:1.5;
  }
  .display{ font-family:'Fraunces', serif; }

  .app{ width:100%; max-width:640px; }

  .setup{ text-align:center; animation:rise .7s ease both; }
  .setup .eyebrow{
    letter-spacing:.22em; text-transform:uppercase; font-size:11px;
    color:var(--rose-dim); margin-bottom:18px; display:block;
  }
  .setup h1{ font-size:clamp(30px,8vw,52px); font-weight:450; font-style:italic; margin-bottom:14px; }
  .setup p.sub{ color:var(--cream-dim); max-width:420px; margin:0 auto 34px; font-size:15px; }

  .mode-switch{
    display:flex; gap:8px; justify-content:center; margin-bottom:30px;
    background:rgba(26,27,46,0.05); border:1px solid var(--line);
    border-radius:100px; padding:4px; max-width:320px; margin-left:auto; margin-right:auto;
  }
  .mode-btn{
    flex:1; background:none; border:none; color:var(--cream-dim);
    font-family:'Inter',sans-serif; font-size:13px; padding:10px 14px;
    border-radius:100px; cursor:pointer; transition:.18s;
  }
  .mode-btn.active{ background:linear-gradient(135deg, var(--rose), var(--gold)); color:var(--ink); font-weight:600; }
  .mode-btn:disabled{ opacity:.45; cursor:not-allowed; }

  .panel{ display:none; }
  .panel.active{ display:block; }

  .name-row{ display:flex; gap:14px; margin-bottom:16px; }
  .name-field{ flex:1; text-align:left; min-width:0; }
  .name-field label{
    font-size:11px; letter-spacing:.14em; text-transform:uppercase;
    display:block; margin-bottom:8px;
  }
  .name-field:nth-child(1) label{ color:var(--rose-dim); }
  .name-field:nth-child(2) label{ color:var(--gold-dim); }

  input[type=text]{
    width:100%; background:rgba(26,27,46,0.06); border:1px solid var(--line);
    border-radius:14px; padding:14px 16px; color:var(--cream);
    font-family:'Inter', sans-serif; font-size:16px; outline:none;
    transition:border-color .2s, background .2s;
  }
  input[type=text]:focus{ border-color:var(--rose-dim); background:rgba(26,27,46,0.1); }
  .name-field:nth-child(2) input:focus{ border-color:var(--gold-dim); }
  input[type=text].code-input{ text-align:center; letter-spacing:.3em; text-transform:uppercase; font-weight:600; }

  .field-solo{ text-align:left; margin-bottom:16px; }
  .field-solo label{
    font-size:11px; letter-spacing:.14em; text-transform:uppercase; color:var(--rose-dim);
    display:block; margin-bottom:8px;
  }

  .start-btn{
    margin-top:6px; background:linear-gradient(135deg, var(--rose), var(--gold));
    border:none; color:var(--ink); font-family:'Inter', sans-serif; font-weight:600;
    font-size:15px; padding:15px 34px; border-radius:100px; cursor:pointer; letter-spacing:.02em;
    transition:transform .18s ease, box-shadow .18s ease;
    box-shadow:0 10px 30px -10px rgba(255,137,102,0.45);
    width:100%; max-width:320px;
  }
  .start-btn:hover{ transform:translateY(-2px); box-shadow:0 14px 34px -8px rgba(255,137,102,0.55); }
  .start-btn:active{ transform:translateY(0); }
  .start-btn:disabled{ opacity:.35; cursor:not-allowed; transform:none; box-shadow:none; }

  .share-box{
    margin-top:22px; border:1px dashed var(--line); border-radius:16px; padding:18px;
    background:rgba(26,27,46,0.04); text-align:left; display:none;
  }
  .share-box.show{ display:block; animation:fadeIn .4s ease both; }
  .share-box .lbl{ font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--cream-dim); margin-bottom:8px; }
  .share-code{
    font-family:'Fraunces',serif; font-size:clamp(24px,7vw,30px); letter-spacing:.15em; font-weight:600;
    color:var(--gold-dim); margin-bottom:8px; word-break:break-all;
  }
  .share-box p{ font-size:12.5px; color:var(--cream-dim); }
  .copy-btn{
    margin-top:12px; background:none; border:1px solid var(--line); color:var(--cream);
    font-size:12px; padding:8px 16px; border-radius:100px; cursor:pointer; font-family:'Inter',sans-serif;
  }
  .copy-btn:hover{ background:rgba(26,27,46,0.08); }

  .err-msg{ color:var(--rose-dim); font-size:12.5px; margin-top:10px; min-height:16px; }

  .main{ display:none; }
  .main.active{ display:block; animation:rise .6s ease both; }

  .topbar{ display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; gap:10px; flex-wrap:wrap; }
  .brand{ font-family:'Fraunces', serif; font-style:italic; font-size:20px; }
  .reset-link{
    font-size:12px; color:var(--cream-dim); text-decoration:none; border-bottom:1px solid transparent;
    cursor:pointer; background:none; border:none; font-family:'Inter',sans-serif;
  }
  .reset-link:hover{ border-bottom-color:var(--cream-dim); }

  .sync-row{
    display:flex; align-items:center; gap:8px; font-size:11px; color:var(--cream-dim);
    margin-bottom:20px; flex-wrap:wrap;
  }
  .sync-dot{ width:6px; height:6px; border-radius:50%; background:var(--gold); animation:pulse 1.8s infinite; flex:0 0 auto; }
  @keyframes pulse{ 0%,100%{opacity:.4;} 50%{opacity:1;} }

  .app-mode-switch{
    display:flex; gap:8px; margin-bottom:22px;
    background:rgba(26,27,46,0.05); border:1px solid var(--line);
    border-radius:100px; padding:4px; flex-wrap:wrap;
  }
  .app-mode-switch .mode-btn{ font-size:13px; min-width:0; }

  .thread-wrap{ margin-bottom:28px; }
  .thread-labels{
    display:flex; justify-content:space-between; font-size:11px; letter-spacing:.08em;
    text-transform:uppercase; color:var(--cream-dim); margin-bottom:8px; gap:6px;
  }
  .thread-labels span{ overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  svg#thread{ width:100%; height:34px; display:block; }

  .theme-nav{
    display:flex; gap:8px; overflow-x:auto; margin-bottom:18px; padding-bottom:6px;
    -webkit-overflow-scrolling:touch;
  }
  .theme-nav::-webkit-scrollbar{ height:4px; }
  .theme-pill{
    flex:0 0 auto; font-size:11px; letter-spacing:.06em; padding:7px 14px; border-radius:100px;
    border:1px solid var(--line); color:var(--cream-dim); background:none; cursor:pointer;
    white-space:nowrap; font-family:'Inter',sans-serif; transition:.18s;
  }
  .theme-pill.active{ background:rgba(26,27,46,0.12); color:var(--cream); border-color:rgba(26,27,46,0.3); }

  .category-tag{
    display:inline-flex; align-items:center; gap:6px; font-size:11px; letter-spacing:.12em;
    text-transform:uppercase; padding:6px 14px; border-radius:100px; margin-bottom:18px;
    border:1px solid var(--line);
  }

  .question-card{
    background:var(--surface);
    border:1px solid var(--line); border-radius:var(--radius); padding:34px 30px; margin-bottom:22px;
    box-shadow:0 18px 40px -24px rgba(26,27,46,0.18);
  }
  .q-number{ font-size:11px; color:var(--cream-dim); letter-spacing:.1em; margin-bottom:10px; }
  .question-card h2{
    font-family:'Fraunces', serif; font-weight:450; font-size:clamp(20px,5vw,27px); line-height:1.3;
  }

  .answers{ display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px; }
  @media (max-width:540px){ .answers{ grid-template-columns:1fr; } }

  .answer-box{
    border-radius:18px; padding:20px; position:relative; min-height:190px;
    display:flex; flex-direction:column; border:1px solid var(--line);
  }
  .answer-box.a{ background:rgba(255,137,102,0.10); }
  .answer-box.b{ background:rgba(91,111,232,0.12); }

  .answer-name{ font-size:12px; font-weight:600; letter-spacing:.04em; margin-bottom:12px; padding-right:70px; }
  .answer-box.a .answer-name{ color:var(--rose-dim); }
  .answer-box.b .answer-name{ color:var(--gold-dim); }

  .answer-box textarea{
    flex:1; background:transparent; border:none; color:var(--cream);
    font-family:'Inter', sans-serif; font-size:16px; resize:none; outline:none; line-height:1.55;
  }
  .answer-box textarea::placeholder{ color:var(--ink-soft); opacity:0.75; }

  .answer-locked{ font-family:'Fraunces', serif; font-style:italic; font-size:14.5px; line-height:1.6; flex:1; }
  .answer-status{
    position:absolute; top:16px; right:18px; font-size:10px; letter-spacing:.1em;
    text-transform:uppercase; color:var(--ink-soft);
  }
  .answer-status.done{ color:var(--cream); }

  .save-btn{
    align-self:flex-start; margin-top:10px; background:none; border:1px solid var(--line);
    color:var(--cream); font-family:'Inter', sans-serif; font-size:12px; padding:10px 16px;
    border-radius:100px; cursor:pointer; transition:.18s;
  }
  .save-btn:hover{ background:rgba(26,27,46,0.1); }
  .save-btn:disabled{ opacity:.5; cursor:wait; }

  .edit-btn{
    margin-top:10px; align-self:flex-start; background:none; border:1px dashed var(--line);
    color:var(--cream-dim); font-family:'Inter', sans-serif; font-size:11.5px; padding:7px 14px;
    border-radius:100px; cursor:pointer; transition:.18s;
  }
  .edit-btn:hover{ background:rgba(26,27,46,0.08); color:var(--cream); }

  .veil{ font-size:12.5px; color:var(--ink-soft); font-style:italic; margin-top:auto; }

  .nav-row{ display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
  .nav-btn{
    background:none; border:1px solid var(--line); color:var(--cream);
    font-family:'Inter', sans-serif; font-size:13px; padding:11px 22px;
    border-radius:100px; cursor:pointer; transition:.18s;
  }
  .nav-btn:hover:not(:disabled){ background:rgba(26,27,46,0.08); border-color:var(--rose-dim); }
  .nav-btn:disabled{ opacity:.3; cursor:not-allowed; }
  .nav-btn.primary{ background:linear-gradient(135deg, var(--rose), var(--gold)); color:var(--ink); font-weight:600; border:none; }

  .game-view{ text-align:center; }
  .dice-row{ display:flex; align-items:center; justify-content:center; gap:22px; margin-bottom:22px; flex-wrap:wrap; }
  .dice-player{ text-align:center; }
  .dice-name{ font-size:12px; letter-spacing:.08em; text-transform:uppercase; color:var(--cream-dim); margin-bottom:10px; }
  .dice-face{
    width:76px; height:76px; border-radius:20px; border:1px solid var(--line);
    background:var(--surface);
    box-shadow:0 10px 22px -14px rgba(26,27,46,0.22);
    display:flex; align-items:center; justify-content:center; font-size:34px;
    transition:transform .15s ease;
  }
  .dice-face.rolling{ animation:shake .12s infinite; }
  @keyframes shake{ 0%,100%{transform:rotate(-4deg);} 50%{transform:rotate(4deg);} }
  .dice-vs{ font-family:'Fraunces',serif; font-style:italic; color:var(--cream-dim); font-size:15px; }

  .dice-roll-btn{
    background:none; border:1px solid var(--line); color:var(--cream);
    font-family:'Inter', sans-serif; font-size:12px; padding:9px 16px;
    border-radius:100px; cursor:pointer; margin-top:10px; transition:.18s;
  }
  .dice-roll-btn:hover:not(:disabled){ background:rgba(26,27,46,0.1); border-color:var(--rose-dim); }
  .dice-roll-btn:disabled{ opacity:.35; cursor:not-allowed; }
  .dice-waiting{ font-size:11px; color:var(--ink-soft); font-style:italic; margin-top:10px; min-height:14px; }

  .dice-result{
    font-family:'Fraunces', serif; font-style:italic; font-size:clamp(16px,4vw,19px);
    min-height:28px; margin-bottom:18px; color:var(--gold-dim);
  }

  .choice-row{ display:flex; gap:14px; justify-content:center; margin-bottom:10px; flex-wrap:wrap; }
  .choice-row .nav-btn{ min-width:130px; }

  .prompt-card{
    background:linear-gradient(160deg, rgba(255,137,102,0.12), rgba(91,111,232,0.08));
    border:1px solid var(--line); border-radius:var(--radius); padding:32px 26px; margin:22px 0;
    text-align:left;
  }
  .prompt-card h2{
    font-family:'Fraunces', serif; font-weight:450; font-size:clamp(19px,4.6vw,25px); line-height:1.35; margin-top:6px;
  }

  .truth-answers-wrap{ margin:18px 0; }
  .truth-answers-wrap .answers{ margin-bottom:14px; }
  .truth-answers-wrap .answer-box{ min-height:120px; }

  .ttt-view{ text-align:center; }
  .ttt-turn{
    font-family:'Fraunces', serif; font-style:italic; font-size:clamp(16px,4vw,19px);
    min-height:26px; margin-bottom:18px; color:var(--gold-dim);
  }
  .ttt-board{
    display:grid; gap:8px;
    max-width:300px; margin:0 auto 22px; aspect-ratio:1/1;
  }
  .ttt-cell{
    background:var(--surface); border:1px solid var(--line); border-radius:14px;
    display:flex; align-items:center; justify-content:center;
    font-family:'Fraunces', serif; cursor:pointer;
    transition:.15s; color:var(--ink);
  }
  .ttt-cell:hover:not(.filled):not(.disabled){ background:rgba(26,27,46,0.06); }
  .ttt-cell.disabled{ cursor:not-allowed; }
  .ttt-cell.x{ color:var(--rose-dim); }
  .ttt-cell.o{ color:var(--gold-dim); }
  .ttt-cell.win{ background:rgba(139,92,246,0.18); border-color:var(--thread); }
  .ttt-score{ font-size:12px; color:var(--cream-dim); margin-bottom:16px; }
  .ttt-score strong{ color:var(--cream); }

  .ttt-series{ font-size:12px; color:var(--cream-dim); margin-bottom:10px; }
  .ttt-series strong{ color:var(--cream); }
  .ttt-steal-row{ display:flex; justify-content:center; gap:16px; margin-bottom:14px; flex-wrap:wrap; }
  .ttt-steal-chip{
    font-size:11.5px; padding:7px 14px; border-radius:100px; border:1px solid var(--line);
    color:var(--cream-dim); display:flex; align-items:center; gap:8px;
  }
  .ttt-steal-chip.mine{ border-color:var(--thread); color:var(--cream); }
  .ttt-steal-btn{
    background:linear-gradient(135deg, var(--thread), var(--gold)); border:none; color:#fff;
    font-family:'Inter',sans-serif; font-size:11px; padding:6px 12px; border-radius:100px; cursor:pointer;
  }
  .ttt-steal-btn:disabled{ opacity:.4; cursor:not-allowed; }
  .ttt-timer{ font-size:11px; color:var(--rose-dim); margin-bottom:8px; min-height:14px; }
  .ttt-toast{
    font-size:12px; color:var(--thread); font-style:italic; margin-bottom:10px; min-height:16px;
  }
  .ttt-winner-choice{
    border:1px dashed var(--line); border-radius:16px; padding:18px; margin:16px 0; text-align:left;
    background:rgba(139,92,246,0.06);
  }
  .ttt-winner-choice .lbl{ font-size:11px; letter-spacing:.1em; text-transform:uppercase; color:var(--cream-dim); margin-bottom:10px; }
  .ttt-winner-choice .choice-row{ justify-content:flex-start; }

  @keyframes rise{ from{ opacity:0; transform:translateY(14px);} to{ opacity:1; transform:translateY(0);} }
  @keyframes fadeIn{ from{ opacity:0; transform:translateY(6px);} to{ opacity:1; transform:translateY(0);} }
  .fade{ animation:fadeIn .45s ease both; }

  html, body{ overflow-x:hidden; }
  button, .theme-pill, .mode-btn, .nav-btn, .start-btn, .dice-roll-btn, .copy-btn, .save-btn, .edit-btn, .ttt-cell{
    touch-action:manipulation; -webkit-tap-highlight-color:transparent;
  }
  input[type=text], textarea{ -webkit-tap-highlight-color:transparent; }

  @media (max-width:600px){
    body{ padding:20px 14px; align-items:flex-start; }
    .app{ max-width:100%; }
    .setup h1{ font-size:clamp(28px,9vw,44px); }
    .question-card{ padding:22px 18px; }
    .prompt-card{ padding:20px 16px; }
    .answer-box{ padding:16px; min-height:140px; }
    .answers{ gap:12px; }
    .answer-box textarea{ font-size:16px; }
    .nav-row{ justify-content:center; }
    .nav-row .nav-btn{ flex:1 1 auto; text-align:center; min-height:46px; }
    #progressLabel{ order:3; width:100%; text-align:center; }
    .thread-labels span{ max-width:46%; }
    .share-code{ letter-spacing:.1em; }
    .mode-switch{ max-width:100%; }
  }
  @media (max-width:420px){
    .name-row{ flex-direction:column; gap:12px; }
    .setup .eyebrow{ font-size:10px; }
    .mode-btn{ font-size:12px; padding:10px 8px; min-height:40px; }
    .start-btn{ width:100%; padding:15px 20px; }
    .theme-pill{ font-size:10.5px; padding:7px 12px; }
    .dice-face{ width:60px; height:60px; font-size:26px; border-radius:16px; }
    .dice-row{ gap:14px; }
    .choice-row{ gap:10px; }
    .choice-row .nav-btn{ flex:1 1 auto; min-width:0; padding:12px 10px; }
    .topbar{ gap:6px; }
    .brand{ font-size:18px; }
    .question-card{ padding:20px 16px; }
    .question-card h2{ font-size:clamp(18px,5.6vw,23px); }
    .answer-box{ min-height:130px; padding:14px; }
    .prompt-card{ padding:18px 14px; }
    input[type=text]{ padding:13px 14px; }
    .ttt-board{ max-width:240px; }
  }
  @media (max-width:340px){
    .dice-row{ gap:10px; }
    .dice-face{ width:52px; height:52px; font-size:22px; }
  }

  @media (prefers-reduced-motion: reduce){ *{ animation:none !important; transition:none !important; } }

  .topbar-actions{ display:flex; align-items:center; gap:10px; }
  .msg-fab{
    position:relative; width:42px; height:42px; border-radius:50%;
    border:1px solid var(--line); background:var(--surface);
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    font-size:18px; transition:.18s; flex:0 0 auto;
    box-shadow:0 8px 20px -12px rgba(26,27,46,0.25);
  }
  .msg-fab:hover{ border-color:var(--rose-dim); transform:translateY(-1px); }
  .msg-badge{
    position:absolute; top:-2px; right:-2px; min-width:16px; height:16px; padding:0 4px;
    border-radius:100px; background:var(--rose); color:#fff; font-size:9px; font-weight:700;
    display:none; align-items:center; justify-content:center; line-height:16px;
  }
  .msg-badge.show{ display:flex; }

  .chat-overlay{
    position:fixed; inset:0; z-index:200; background:rgba(26,27,46,0.45);
    display:none; align-items:flex-end; justify-content:center;
    padding:0 max(12px, env(safe-area-inset-right)) max(12px, env(safe-area-inset-bottom)) max(12px, env(safe-area-inset-left));
  }
  .chat-overlay.open{ display:flex; animation:fadeIn .25s ease both; }
  .chat-panel{
    width:100%; max-width:640px; max-height:min(82vh, 720px); background:var(--surface);
    border:1px solid var(--line); border-radius:22px 22px 18px 18px;
    display:flex; flex-direction:column; overflow:hidden;
    box-shadow:0 24px 60px -20px rgba(26,27,46,0.35);
  }
  .chat-head{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:14px 16px; border-bottom:1px solid var(--line);
  }
  .chat-head h3{ font-family:'Fraunces',serif; font-size:17px; font-weight:450; font-style:italic; }
  .chat-close{
    background:none; border:none; font-size:22px; line-height:1; cursor:pointer; color:var(--cream-dim); padding:4px 8px;
  }
  .chat-sub{ font-size:11px; color:var(--cream-dim); padding:0 16px 10px; }
  .chat-messages{
    flex:1; overflow-y:auto; padding:12px 14px 8px; display:flex; flex-direction:column; gap:10px;
    -webkit-overflow-scrolling:touch;
  }
  .chat-bubble{
    max-width:85%; padding:10px 14px; border-radius:16px; font-size:14.5px; line-height:1.45;
    word-break:break-word;
  }
  .chat-bubble.mine{ align-self:flex-end; background:rgba(255,137,102,0.18); border:1px solid rgba(255,137,102,0.35); }
  .chat-bubble.theirs{ align-self:flex-start; background:rgba(91,111,232,0.14); border:1px solid rgba(91,111,232,0.28); }
  .chat-meta{ font-size:10px; color:var(--ink-soft); margin-bottom:4px; letter-spacing:.04em; }
  .chat-compose{
    display:flex; gap:8px; padding:12px 14px; border-top:1px solid var(--line);
    background:rgba(26,27,46,0.03);
  }
  .chat-compose input{
    flex:1; min-width:0; border-radius:100px; padding:12px 16px; font-size:15px;
  }
  .chat-send{
    flex:0 0 auto; border:none; border-radius:100px; padding:0 18px; min-height:44px;
    background:linear-gradient(135deg, var(--rose), var(--gold)); color:var(--ink); font-weight:600;
    font-family:'Inter',sans-serif; cursor:pointer;
  }
  .chat-send:disabled{ opacity:.45; cursor:not-allowed; }
  .chat-empty{ text-align:center; color:var(--cream-dim); font-size:13px; font-style:italic; padding:24px 12px; }
</style>
<style>
    @supports (-webkit-overflow-scrolling: touch) {
  select:focus,
  textarea:focus,
  input:focus {
  font-size: 16px;
  }
  }
</style>  
</head>
<body>

<div class="app">

  <!-- SETUP -->
  <section class="setup" id="setup">
    <span class="eyebrow"> <h2>Un espace pour deux</h2></span>
    <h1>Deux voix, une histoire</h1>
    <p class="sub">Chacun répond de son côté, où qu'il soit. Les réponses se révèlent seulement quand vous avez tous les deux écrit vos réponses, pas de triche, juste de la vérité.</p>

    <div class="mode-switch">
      <button class="mode-btn active" id="modeCreate">Créer un espace</button>
      <button class="mode-btn" id="modeJoin">Rejoindre avec un code</button>
    </div>

    <div class="panel active" id="panelCreate">
      <div class="name-row">
        <div class="name-field">
          <label>Ton prénom</label>
          <input type="text" id="nameA" placeholder="ex. Léa" maxlength="18">
        </div>
        <div class="name-field">
          <label>Le prénom de l'autre</label>
          <input type="text" id="nameB" placeholder="ex. Nathan" maxlength="18">
        </div>
      </div>
      <button class="start-btn" id="createBtn" disabled>Créer notre espace</button>

      <div class="share-box" id="shareBox">
        <div class="lbl">Votre code</div>
        <div class="share-code" id="shareCode"></div>
        <p>Envoie ce code à <span id="shareTarget"></span> par message. Il·elle l'entre dans « Rejoindre avec un code » sur son téléphone — vous n'avez pas besoin d'être ensemble.</p>
        <button class="copy-btn" id="copyBtn">Copier le code</button>
        <button class="start-btn" id="enterBtn" style="margin-top:16px; display:block; width:100%;">Entrer dans notre espace →</button>
      </div>
    </div>

    <div class="panel" id="panelJoin">
      <div class="field-solo">
        <label>Code partagé par l'autre</label>
        <input type="text" id="joinCode" class="code-input" placeholder="XXXXXX" maxlength="6">
      </div>
      <div class="field-solo">
        <label>Ton prénom</label>
        <input type="text" id="joinName" placeholder="ex. Nathan" maxlength="18">
      </div>
      <button class="start-btn" id="joinBtn" disabled>Rejoindre</button>
      <div class="err-msg" id="joinErr"></div>
    </div>
  </section>

  <!-- MAIN -->
  <section class="main" id="main">
    <div class="topbar">
      <div class="brand">Deux</div>
      <div class="topbar-actions">
        <button type="button" class="msg-fab" id="openChatBtn" title="Messages à deux" aria-label="Ouvrir la discussion">
          💬
          <span class="msg-badge" id="chatBadge"></span>
        </button>
        <button class="reset-link" id="resetBtn">quitter cet espace</button>
      </div>
    </div>
    <div class="sync-row"><span class="sync-dot"></span><span id="syncLabel">connecté à l'espace <strong id="codeLabel"></strong></span></div>

    <div class="app-mode-switch">
      <button class="mode-btn active" id="modeQuestionsBtn">Questions</button>
      <button class="mode-btn" id="modeGameBtn">Action ou Vérité 🎲</button>
      <button class="mode-btn" id="modeTttBtn">Morpion ❌⭕</button>
    </div>

    <!-- VUE QUESTIONS -->
    <div id="questionsView">
      <div class="thread-wrap">
        <div class="thread-labels">
          <span id="labelA"></span>
          <span id="labelB"></span>
        </div>
        <svg id="thread" viewBox="0 0 400 34" preserveAspectRatio="none">
          <line x1="6" y1="17" x2="394" y2="17" stroke="rgba(26,27,46,0.12)" stroke-width="2"/>
          <line id="threadFill" x1="6" y1="17" x2="6" y2="17" stroke="#FF8966" stroke-width="2"/>
          <circle cx="6" cy="17" r="6" fill="var(--rose)"/>
          <circle id="threadDot" cx="6" cy="17" r="5" fill="var(--gold)"/>
        </svg>
      </div>

      <div class="theme-nav" id="themeNav"></div>

      <span class="category-tag" id="catTag"></span>

      <div class="question-card fade" id="qCard">
        <div class="q-number" id="qNumber"></div>
        <h2 id="qText"></h2>
      </div>

      <div class="answers">
        <div class="answer-box a">
          <span class="answer-status" id="statusA"></span>
          <div class="answer-name" id="nameLabelA"></div>
          <div id="slotA"></div>
        </div>
        <div class="answer-box b">
          <span class="answer-status" id="statusB"></span>
          <div class="answer-name" id="nameLabelB"></div>
          <div id="slotB"></div>
        </div>
      </div>

      <div class="nav-row">
        <button class="nav-btn" id="prevBtn">← précédente</button>
        <span style="font-size:12px;color:var(--cream-dim)" id="progressLabel"></span>
        <button class="nav-btn primary" id="nextBtn">suivante →</button>
      </div>
    </div>

    <!-- VUE JEU ACTION OU VÉRITÉ -->
    <div id="gameView" class="game-view" style="display:none;">
      <p class="sub" style="color:var(--cream-dim); font-size:13.5px; max-width:420px; margin:0 auto 20px;">
        Chacun lance son dé depuis son écran. Le score le plus bas doit se soumettre : action ou vérité, à vous de choisir — puis c'est le hasard qui décide laquelle.
      </p>

      <div class="mode-switch" id="difficultySwitch" style="margin-bottom:26px;">
        <button class="mode-btn active" id="modeSoftBtn">Soft</button>
        <button class="mode-btn" id="modeHardBtn">Hard 🔥</button>
      </div>

      <div class="dice-row">
        <div class="dice-player">
          <div class="dice-name" id="diceNameA"></div>
          <div class="dice-face" id="diceFaceA">🎲</div>
          <button class="dice-roll-btn" id="rollBtnA">Lancer mon dé</button>
          <div class="dice-waiting" id="diceWaitA"></div>
        </div>
        <div class="dice-vs">contre</div>
        <div class="dice-player">
          <div class="dice-name" id="diceNameB"></div>
          <div class="dice-face" id="diceFaceB">🎲</div>
          <button class="dice-roll-btn" id="rollBtnB">Lancer mon dé</button>
          <div class="dice-waiting" id="diceWaitB"></div>
        </div>
      </div>

      <div class="dice-result" id="diceResult"></div>

      <div class="choice-row" id="choiceRow" style="display:none;">
        <button class="nav-btn primary" id="chooseTruth">Vérité</button>
        <button class="nav-btn primary" id="chooseAction">Action</button>
      </div>

      <div class="prompt-card fade" id="promptCard" style="display:none;">
        <div class="q-number" id="promptLabel"></div>
        <h2 id="promptText"></h2>

        <div class="truth-answers-wrap" id="truthAnswersWrap" style="display:none;">
          <div class="answers">
            <div class="answer-box a">
              <span class="answer-status" id="truthStatusA"></span>
              <div class="answer-name" id="truthNameLabelA"></div>
              <div id="truthSlotA"></div>
            </div>
            <div class="answer-box b">
              <span class="answer-status" id="truthStatusB"></span>
              <div class="answer-name" id="truthNameLabelB"></div>
              <div id="truthSlotB"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="nav-row" id="preConfirmRow" style="display:none; justify-content:center; gap:12px;">
        <button class="nav-btn" id="rerollPrompt">une autre →</button>
        <button class="nav-btn primary" id="confirmDoneBtn">J'ai terminé ✓</button>
      </div>

      <div class="dice-waiting" id="confirmWaitLabel" style="min-height:auto; margin-bottom:10px;"></div>

      <div class="nav-row" id="promptNavRow" style="display:none; justify-content:center; gap:12px;">
        <button class="nav-btn primary" id="newRoundBtn">nouveau tour 🎲</button>
      </div>
    </div>

    <!-- VUE MORPION -->
    <div id="tttView" class="ttt-view" style="display:none;">
      <p class="sub" style="color:var(--cream-dim); font-size:13.5px; max-width:420px; margin:0 auto 16px;">
        <span id="tttSymbolNote"></span>
      </p>

      <div class="mode-switch" id="tttSizeSwitch" style="margin-bottom:14px;">
        <button class="mode-btn active" id="tttSize3Btn">3×3</button>
        <button class="mode-btn" id="tttSize4Btn">4×4</button>
        <button class="mode-btn" id="tttSize5Btn">5×5</button>
      </div>

      <div class="mode-switch" id="tttBestOfSwitch" style="margin-bottom:10px; max-width:260px;">
        <button class="mode-btn active" id="tttBo3Btn">Best of 3</button>
        <button class="mode-btn" id="tttBo5Btn">Best of 5</button>
      </div>

      <div class="mode-switch" id="tttSuddenSwitch" style="margin-bottom:18px; max-width:260px;">
        <button class="mode-btn active" id="tttNormalBtn">Normal</button>
        <button class="mode-btn" id="tttSuddenBtn">Mort subite ⚡</button>
      </div>

      <div class="ttt-series" id="tttSeries"></div>
      <div class="ttt-score" id="tttScore"></div>

      <div class="ttt-steal-row" id="tttStealRow"></div>
      <div class="ttt-toast" id="tttToast"></div>
      <div class="ttt-timer" id="tttTimer"></div>

      <div class="ttt-turn" id="tttTurn"></div>
      <div class="ttt-board" id="tttBoard"></div>

      <div class="ttt-winner-choice" id="tttWinnerChoice" style="display:none;">
        <div class="lbl" id="tttWinnerChoiceLabel"></div>
        <div class="choice-row">
          <button class="nav-btn primary" id="tttPickThemeBtn">Choisir le prochain thème</button>
          <button class="nav-btn primary" id="tttPickHardBtn">Passer Action/Vérité en Hard 🔥</button>
          <button class="nav-btn primary" id="tttPickSoftBtn">Passer Action/Vérité en Soft</button>
        </div>
      </div>

      <div class="nav-row" style="justify-content:center;">
        <button class="nav-btn primary" id="tttResetBtn">nouvelle partie ↺</button>
      </div>
    </div>

  </section>

  <div class="chat-overlay" id="chatOverlay" aria-hidden="true">
    <div class="chat-panel" role="dialog" aria-labelledby="chatTitle">
      <div class="chat-head">
        <h3 id="chatTitle">Discussion privée</h3>
        <button type="button" class="chat-close" id="closeChatBtn" aria-label="Fermer">×</button>
      </div>
      <p class="chat-sub" id="chatSub"></p>
      <div class="chat-messages" id="chatMessages"></div>
      <div class="chat-compose">
        <input type="text" id="chatInput" placeholder="Écris un message…" maxlength="2000" autocomplete="off">
        <button type="button" class="chat-send" id="chatSendBtn">Envoyer</button>
      </div>
    </div>
  </div>

</div>

<script>

(function(){
  const THEMES = [
    {
      key:"attentes", label:"Attentes", tagClass:"cat-leger", color:"var(--gold-dim)",
      questions:[
        "Concrètement, qu'attends-tu de moi quand tu rentres épuisé(e) : écoute, silence, câlin, ou espace — et dans quel ordre ?",
        "Quelle fréquence minimale de messages ou d'appels te fait sentir que je pense à toi sans que ce soit étouffant ?",
        "Qu'est-ce que tu attends de moi quand tu as une mauvaise nouvelle : que je trouve des solutions ou que je reste juste là ?",
        "Quelle place veux-tu que j'occupe dans tes amitiés : présent(e) aux sorties, en retrait, ou au cas par cas — explique avec un exemple.",
        "Qu'attends-tu de moi sur la jalousie : transparence totale, confiance aveugle, ou règles précises — lesquelles ?",
        "Quelle est la promesse non dite que tu attends de moi dans ce couple (fidélité émotionnelle, priorités, temps…) ?",
        "Qu'attends-tu de moi quand on n'est pas d'accord : débat immédiat, pause, ou écrit le lendemain ?",
        "Est-ce que tu penses que tu m'aimes ? Ou que ressens-tu pour moi ?",  
        "Quel niveau d'initiative veux-tu de ma part pour organiser dates, sexe, projets — 50/50 ou que je prenne les rênes parfois ?",
        "Qu'attends-tu que je fasse quand tu dis « ça va » alors que ce n'est clairement pas le cas ?",
        "Quelle reconnaissance concrète (mots, gestes, cadeaux) te fait sentir que ton effort dans le couple est vu ?",
        "Qu'attends-tu de moi vis-à-vis de ta famille : soutien inconditionnel, neutralité, ou limites claires avec eux ?",
        "Si tu devais formuler une « fiche de poste » du partenaire idéal pour toi aujourd'hui, quelles seraient les 3 lignes non négociables ?",
        "Qu'attends-tu de moi quand tu réussis quelque chose : célébration bruyante, fierté discrète, ou partage sur les réseaux — ou non ?",
        "Quelle transparence attends-tu sur l'argent que je dépense seul(e) (seuil au-delà duquel tu veux être prévenu(e)) ?"
      ]
    },
    {
      key:"engagement", label:"Engagement", tagClass:"cat-profond", color:"var(--rose-dim)",
      questions:[
        "Qu'est-ce qui, chez moi ou dans notre dynamique, te ferait sérieusement remettre en question l'avenir du couple ?",
        "Quelle trahison (même « petite ») serait irréparable pour toi : mensonge, flirt, secret financier, autre ?",
        "Es-tu exclusivement avec moi aujourd'hui — émotionnellement et physiquement — et y a-t-il une zone grise dont je devrais savoir ?",
        "Dans combien de temps veux-tu qu'on ait clarifié notre projet commun (mariage, PACS, enfants, colocation) — et quelle étape en premier ?",
        "Quelle part de ta liberté personnelle refuses-tu de sacrifier, même pour moi ?",
        "Si on devait signer un contrat de couple honnête ce soir, quelle clause protégerais-tu en premier ?",
        "Qu'est-ce que « rester ensemble pour les bonnes raisons » signifie pour toi — pas par peur, pas par habitude ?",
        "As-tu encore des sentiments ou des liens avec un·e ex que je devrais connaître pour construire en confiance ?",
        "Quel engagement concret veux-tu que je prenne cette année (thérapie de couple, budget commun, déménagement…) ?",
        "Peux-tu me dire une chose que tu n'as pas encore totalement « choisie » en restant avec moi ?",
        "Comment définis-tu la loyauté dans un couple : qu'est-ce qui est OK avec d'autres, et qu'est-ce qui ne l'est jamais ?",
        "Quelle preuve d'engagement de ma part te manque le plus aujourd'hui ?"
      ]
    },
    {
      key:"profond", label:"Profond", tagClass:"cat-profond", color:"var(--rose-dim)",
      questions:[
        "Quelle vérité sur toi dans ce couple n'as-tu jamais osé formuler clairement ?",
        "Qu'est-ce que tu me reproches en silence depuis plus de six mois ?",
        "Quelle peur d'enfance ou de relation passée influence encore ta façon de réagir avec moi ?",
        "Quand te sens-tu le moins en sécurité émotionnellement avec moi — décris une situation précise.",
        "Qu'est-ce que tu as besoin d'entendre de ma bouche pour arrêter de douter de mon amour ?",
        "Y a-t-il quelque chose que tu m'as pardonné sans avoir vraiment digéré — quoi, et qu'est-ce qu'il te faudrait ?",
        "Quelle part de toi as-tu l'impression de cacher pour rester « facile à aimer » ?",
        "Qu'est-ce que tu voudrais que je comprenne sur ta façon de dire « je t'aime » sans toujours le prononcer ?",
        "Quel est le sujet tabou entre nous que tu évites parce que tu crains ma réaction ?",
        "Si tu pleurais devant moi sans filtre, qu'est-ce qui sortirait en premier ?",
        "Qu'est-ce que tu admires chez moi que tu n'arrives pas à te dire à toi-même ?",
        "Quelle conversation difficile devrions-nous avoir dans les trente prochains jours — laquelle, et pourquoi maintenant ?",
        "Qu'est-ce que tu as appris sur ta capacité à aimer grâce à nous — y compris ce qui te fait mal ?",
        "Quelle question sur notre passé n'as-tu jamais osé me poser par peur de la réponse ?"
      ]
    },
    {
      key:"quotidien", label:"Vie à deux", tagClass:"cat-leger", color:"var(--gold-dim)",
      questions:[
        "Comment veux-tu qu'on répartisse les tâches maison si on vit ensemble — liste concrète (cuisine, ménage, admin) ?",
        "Quel budget mensuel « fun perso » chacun devrait-il garder sans justifier — quel montant te semble juste ?",
        "Combien de soirées par semaine veux-tu qu'on soit vraiment à deux, sans écrans ni invités ?",
        "Comment veux-tu qu'on gère une dispute devant des amis ou la famille : front uni ou honnêteté immédiate ?",
        "Quelle limite poses-tu sur le travail à la maison (mails le soir, week-end) pour protéger le couple ?",
        "Comment veux-tu qu'on décide d'un gros achat (> X €) — seuil et processus ?",
        "Quelle place veux-tu pour le sexe dans l'agenda : spontané, planifié, ou les deux — et à quelle fréquence idéale ?",
        "Comment veux-tu qu'on parle d'enfants (ou absence d'enfants) si le sujet n'est pas encore tranché ?",
        "Quelle habitude à moi te use le plus au quotidien, et quelle alternative te conviendrait ?",
        "Quel rituel hebdomadaire veux-tu qu'on installe pour ne pas devenir deux colocataires ?",
        "Comment veux-tu qu'on gère les fêtes de famille quand nos attentes ne matchent pas ?"
      ]
    },
    {
      key:"confiance", label:"Confiance", tagClass:"cat-profond", color:"#7C8AE8",
      questions:[
        "As-tu déjà fouillé dans mon téléphone ou mes messages — oui/non — et qu'est-ce que ça te dit sur ta confiance ?",
        "Quel secret (le mien ou le tien) pèse encore sur notre relation ?",
        "Qu'est-ce qui te ferait dire « je peux tout lui dire » — et qu'est-ce qui manque encore ?",
        "Y a-t-il une personne dans ton entourage qui influence négativement ton image de nous — qui, et comment ?",
        "Quelle vérité sur ton passé amoureux devrais-je connaître pour mieux te comprendre aujourd'hui ?",
        "À quel moment as-tu le plus douté de moi, et qu'est-ce qui t'a fait rester ou te rassurer ?",
        "Quelle transparence veux-tu sur mes amitiés avec des personnes qui t'attirent ou m'attirent ?",
        "Qu'est-ce que je pourrais faire demain pour reconstruire une confiance que tu sens fragile ?",
        "As-tu déjà minimisé quelque chose d'important avec moi pour éviter un conflit — quoi ?",
        "Quelle promesse que je t'ai faite tiens-tu pour acquise sans vérifier — et est-ce justifié ?"
      ]
    },
    {
      key:"intime", label:"Intime 🔥", tagClass:"cat-intime", color:"#B49BFA",
      questions:[
        "Décris précisément ce que tu veux que je fasse avec ma bouche sur toi la prochaine fois — sans détour.",
        "Quel est ton fantasme le plus sale avec moi que tu n'as jamais osé demander noir sur blanc ?",
        "Quelle partie de ton corps veux-tu que je worshippe plus longtemps — et comment (lent, ferme, yeux dans les yeux) ?",
        "Préfères-tu qu'on domine à tour de rôle, qu'un(e) mène toujours, ou qu'on se provoque — lequel te fait le plus bander / mouiller ?",
        "Quel mot sale ou surnom au lit veux-tu m'entendre dire — ou que je te fasse dire ?",
        "Quelle limite « classée tabou » serais-tu prêt(e) à explorer avec moi si on y va doucement — laquelle ?",
        "Raconte la dernière fois où tu t'es touché(e) en pensant à moi : qu'est-ce que tu te disais, qu'est-ce que tu voulais que je fasse ?",
        "Veux-tu qu'on filme ou qu'on s'envoie des voix coquines — oui/non, et avec quelles règles de confidentialité ?",
        "Quel endroit public ou semi-public t'excite à l'idée qu'on s'y touche sans se faire prendre ?",
        "Qu'est-ce qui te fait le plus jouir : le rythme, la profondeur, les mots, les mains ailleurs — classe-les honnêtement.",
        "As-tu envie qu'on intègre des jouets, des liens, un miroir, une ceinture — lequel te tente en premier ?",
        "Quelle scène porno ou érotique aimerais-tu qu'on recrée ensemble, même approximativement ?",
        "Quand tu me regardes en silence, quelle pensée sexuelle non dite passes-tu le plus souvent ?",
        "Qu'est-ce que je fais déjà au lit que tu veux en plus grande quantité — sois explicite.",
        "Veux-tu qu'on fixe une « safe word » et des signaux pour pousser plus loin sans peur — laquelle choisis-tu ?",
        "Quel est ton kink ou ta pratique secrète que tu crains que je juge — décris-le et dis ce que tu espères de ma réaction."
      ]
    },
    {
      key:"desir", label:"Désir", tagClass:"cat-intime", color:"#9B6BFA",
      questions:[
        "Sur une échelle de 1 à 10, ton désir pour moi cette semaine — et qu'est-ce qui l'a monté ou baissé ?",
        "À quelle fréquence idéale voudrais-tu qu'on fasse l'amour — nombre honnête, pas la réponse « politique » ?",
        "Qu'est-ce qui te met instantanément dans l'ambiance chez moi (odeur, voix, tenue, geste) — le plus efficace ?",
        "Quand as-tu senti pour la dernière fois que je te désirais vraiment, pas par habitude — raconte la scène.",
        "Qu'est-ce qui te bloque encore pour me montrer ton corps sans retenue (lumière, position, cicatrice, autre) ?",
        "Préfères-tu l'initiative sexuelle de ma part le matin, le soir, ou au milieu d'une journée ordinaire ?",
        "Quelle forme de rejet sexuel de ma part te blesse le plus — et comment voudrais-tu que je le formule ?",
        "Y a-t-il une pratique que tu faisais avant nous que tu regrettes d'avoir mise de côté — laquelle ?",
        "Quel compliment sur ton corps ou ton énergie sexuelle veux-tu entendre plus souvent de moi ?",
        "Si on avait une nuit sans limite de temps ni fatigue, par quoi commencerais-tu avec moi — étape par étape ?"
      ]
    }
  ];

  const QUESTIONS = [];
  THEMES.forEach(t => t.questions.forEach(q => QUESTIONS.push({theme:t.key, label:t.label, tagClass:t.tagClass, text:q})));

  const TRUTHS_SOFT = [
    "Quelle est la chose la plus étrange que tu trouves attachante chez moi ?",
    "Quel jour, cette semaine, tu as le plus pensé à moi sans raison particulière ?",
    "Quel est ton pire mensonge tout doux pour me faire plaisir (\"non non, c'est délicieux\") ?",
    "Si tu devais me choisir un deuxième prénom, ce serait lequel ?",
    "Quelle est la chose que je fais et que tu trouves plus drôle que moi je ne le crois ?",
    "Quel est ton souvenir le plus embarrassant devant moi ?",
    "Quelle est la dernière chose que tu as apprise sur moi qui t'a surpris(e) ?",
    "Si on devait fonder un mini-club juste tous les deux, ce serait autour de quoi ?",
    "Quelle chose du quotidien deviendrait insupportable sans moi à côté ?",
    "Quel est ton talent caché que je ne connais peut-être pas encore ?",
    "Quelle habitude de couple aimerais-tu qu'on ajoute à notre liste ?",
    "Quel est le compliment que tu te fais à toi-même en secret ?",
    "Quelle photo de toi tu détestes mais que j'adore ?",
    "Quel est le petit mensonge que tu m'as raconté et que tu regrettes un peu ?",
    "Si tu devais résumer notre semaine en un emoji, ce serait lequel ?"
  ];

  const ACTIONS_SOFT = [
    "Fais une déclaration d'amour en changeant ta voix (robot, star de cinéma, présentateur météo, à toi de choisir).",
    "Trouve trois points communs entre nous en moins de 20 secondes.",
    "Fais-moi un dessin express de nous deux sur un bout de papier ou dans les airs.",
    "Raconte-moi ta version de notre premier vrai fou rire ensemble.",
    "Invente un petit surnom rigolo pour moi, là, maintenant.",
    "Mime ton émotion préférée quand tu me vois arriver.",
    "Fais-moi un compliment en langage soutenu, comme si on était au 18e siècle.",
    "Propose une activité qu'on n'a jamais faite ensemble et qu'on essaie ce mois-ci.",
    "Chante ou fredonne la chanson qui te fait le plus penser à nous.",
    "Raconte un souvenir gênant de ton enfance que je ne connais pas encore.",
    "Fais-moi deviner un film ou une série juste en mimant une scène.",
    "Décris, en 15 secondes chrono, ton moment préféré de la semaine avec moi.",
    "Improvise un petit poème de deux lignes sur nous, même si c'est nul.",
    "Fais-moi la liste de trois choses que tu changerais dans notre appartement/notre routine, version fun.",
    "Propose un défi léger qu'on relève ensemble avant la fin de la semaine."
  ];

  const TRUTHS_HARD = [
    "Quelle est la pensée la plus interdite que tu aies eue à mon sujet cette semaine ?",
    "Si tu devais réécrire notre dernière nuit ensemble, qu'est-ce que tu changerais ?",
    "Quel est le message le plus osé que tu aies tapé et jamais envoyé ?",
    "Quelle partie de mon corps tu regardes le plus quand je ne m'en rends pas compte ?",
    "Sur une échelle de 1 à 10, à quel point as-tu envie de moi là, maintenant ?",
    "Quel est le scénario que tu rejoues le plus souvent dans ta tête ?",
    "Qu'est-ce qui te retient le plus de me dire tes envies les plus osées ?",
    "Quelle est la tenue (ou l'absence de tenue) qui te rend fou/folle chez moi ?",
    "Raconte le moment où tu as eu le plus de mal à te contrôler devant moi.",
    "Quel est le mot ou le son que je fais qui t'excite le plus ?",
    "Si je te donnais carte blanche pour cette nuit, tu commencerais par où ?",
    "Quelle limite n'as-tu jamais osé me proposer de repousser ?",
    "Quel est ton fantasme récurrent que tu n'as encore jamais partagé avec moi ?",
    "Quel souvenir de nous rejoues-tu quand tu es seul(e) et que tu penses à moi ?",
    "Qu'est-ce qui te ferait perdre tout contrôle si je le faisais là, maintenant ?"
  ];

  const ACTIONS_HARD = [
    "Guide ma main vers l'endroit où tu as le plus envie d'être touché(e) en ce moment.",
    "Chuchote-moi à l'oreille, sans filtre, ce que tu as envie qu'on fasse ce soir.",
    "Embrasse-moi comme si c'était la toute première fois.",
    "Enlève un vêtement de mon choix, lentement.",
    "Laisse-moi t'embrasser où je veux pendant 30 secondes, sans bouger.",
    "Décris à voix haute, en détail, ta position préférée avec moi.",
    "Trace du doigt un chemin lent depuis mon oreille jusqu'à ma clavicule.",
    "Montre-moi, sans un mot, ton geste préféré pour commencer une soirée à deux.",
    "Assieds-toi face à moi, très proche, et regarde-moi dans les yeux pendant une minute complète.",
    "Chuchote la fin de ta phrase la plus coquine, celle que tu n'as jamais terminée.",
    "Choisis un endroit de la pièce et emmène-moi là pour le reste de la soirée.",
    "Décris, geste à l'appui, comment tu aimes être déshabillé(e).",
    "Fais-moi languir : approche-toi, puis arrête-toi à un souffle de mes lèvres pendant 10 secondes.",
    "Envoie-moi maintenant, par message, la phrase la plus explicite que tu oses écrire.",
    "Propose-moi un « oui » ou un « défi » pour la suite de la nuit — et tiens ta promesse."
  ];

  let usedTruthsSoft = [];
  let usedActionsSoft = [];
  let usedTruthsHard = [];
  let usedActionsHard = [];
  function pickRandom(list, used){
    if(used.length >= list.length) used = [];
    let idx;
    do{ idx = Math.floor(Math.random()*list.length); }while(used.includes(idx));
    used.push(idx);
    return {text:list[idx], used};
  }

  const SS_KEY = 'deux_local';
  const local = {
    get(){ try{ return JSON.parse(localStorage.getItem(SS_KEY)||'{}'); }catch(e){ return {}; } },
    set(v){ localStorage.setItem(SS_KEY, JSON.stringify(v)); }
  };

  let session = null;
  let myRole = null;
  let pollTimer = null;
  let localMutationBusy = false;
  let lastGameSnapshot = null;
  let lastAnswersSnapshot = null;
  let lastTttSnapshot = null;
  let lastChatSnapshot = null;
  let chatOpen = false;
  let chatReadCount = 0;
  let chatSending = false;
  const editingQuestions = new Set();

  // ---------- Bonus solidité : suivi des sauvegardes de réponses en cours ----------
  // Empêche de changer de question / thème pendant qu'un envoi réseau est en vol,
  // et sert de garde pour ne jamais réécrire le DOM d'une question qu'on a quittée.
  let pendingAnswerSave = false;

  function genCode(){
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let c = '';
    for(let i=0;i<6;i++) c += chars[Math.floor(Math.random()*chars.length)];
    return c;
  }

  function emptyGame(){
    return { round:0, mode:'soft', diceA:null, diceB:null, tieLoser:null, choice:null, promptType:null, promptText:null, confirmed:false, truthAnswers:{a:'', b:''} };
  }

  function emptyTtt(){
    return {
      size:3, board:Array(9).fill(null), turn:'a', winner:null, line:null,
      scoreA:0, scoreB:0, scoreTie:0,
      bestOf:3, seriesA:0, seriesB:0, seriesWinner:null, seriesRound:1,
      sudden:false,
      stealsA:0, stealsB:0,
      lastToast:null,
      winnerChoiceDone:false
    };
  }

  const API = '/session';

  async function loadSession(code){
    const res = await fetch(API + '/' + encodeURIComponent(code) + '?t=' + Date.now(), {
      cache: 'no-store',
      headers: { 'Cache-Control': 'no-cache', 'Accept': 'application/json' }
    });
    if(res.status === 404) return null;
    if(!res.ok) throw new Error('load failed');
    const s = await res.json();
    if(!s.game) s.game = emptyGame();
    else{
      if(s.game.mode == null) s.game.mode = 'soft';
      if(s.game.confirmed == null) s.game.confirmed = false;
      if(s.game.truthAnswers == null) s.game.truthAnswers = {a:'', b:''};
    }
    if(!s.ttt) s.ttt = emptyTtt();
    else{
      const d = emptyTtt();
      for(const k in d){ if(s.ttt[k] === undefined) s.ttt[k] = d[k]; }
      const n = s.ttt.size * s.ttt.size;
      if(!Array.isArray(s.ttt.board) || s.ttt.board.length !== n) s.ttt.board = Array(n).fill(null);
    }
    if(!s.answers) s.answers = {};
    if(!Array.isArray(s.chat)) s.chat = [];
    return s;
  }
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  async function saveSession(s){
    const res = await fetch(API, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify(s)
    });
    if(!res.ok) throw new Error('save failed');
    return await res.json();
  }

  /*
   * IMPORTANT — correctif "lost update" :
   * Auparavant, valider une réponse rechargeait TOUT l'objet session, le modifiait
   * localement, puis le renvoyait en entier via saveSession(). Si les deux
   * partenaires validaient une réponse à quelques centaines de ms d'intervalle,
   * celui qui terminait en second écrasait purement et simplement la réponse
   * que l'autre venait tout juste d'enregistrer (elle n'existait pas encore dans
   * sa copie locale chargée avant). C'est ce qui causait "je ne vois pas la
   * réponse de l'autre" de façon aléatoire.
   *
   * Le correctif : on utilise un endpoint de patch dédié, sur le même principe
   * que /session/{code}/game et /session/{code}/ttt, qui doit fusionner la
   * réponse UNIQUEMENT pour (questionIdx, key) côté serveur, de façon atomique,
   * sans jamais réécrire les autres réponses déjà présentes.
   *
   * ⚠️ Coté backend (Laravel), la route POST /session/{code}/answers doit
   * exister et faire l'équivalent de :
   *   $session->answers[$questionIdx][$key] = $value;
   *   $session->save();
   * dans une transaction / avec un verrou de ligne (lockForUpdate()) pour
   * éviter toute course également côté serveur.
   */
  async function patchAnswer(questionIdx, key, val){
    const res = await fetch(`${API}/${encodeURIComponent(session.code)}/answers`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({ idx: questionIdx, key, value: val })
    });
    if(!res.ok) throw new Error('patch answer failed');
    const s = await res.json();
    return s.answers || {};
  }

  const modeCreate = document.getElementById('modeCreate');
  const modeJoin = document.getElementById('modeJoin');
  const panelCreate = document.getElementById('panelCreate');
  const panelJoin = document.getElementById('panelJoin');

  modeCreate.addEventListener('click', () => {
    modeCreate.classList.add('active'); modeJoin.classList.remove('active');
    panelCreate.classList.add('active'); panelJoin.classList.remove('active');
  });
  modeJoin.addEventListener('click', () => {
    modeJoin.classList.add('active'); modeCreate.classList.remove('active');
    panelJoin.classList.add('active'); panelCreate.classList.remove('active');
  });

  const nameA = document.getElementById('nameA');
  const nameB = document.getElementById('nameB');
  const createBtn = document.getElementById('createBtn');
  const shareBox = document.getElementById('shareBox');
  const shareCode = document.getElementById('shareCode');
  const shareTarget = document.getElementById('shareTarget');
  const copyBtn = document.getElementById('copyBtn');
  const enterBtn = document.getElementById('enterBtn');

  function checkCreate(){ createBtn.disabled = !(nameA.value.trim() && nameB.value.trim()); }
  nameA.addEventListener('input', checkCreate);
  nameB.addEventListener('input', checkCreate);

  let pendingSession = null;

  createBtn.addEventListener('click', async () => {
    createBtn.disabled = true; createBtn.textContent = 'Création…';
    const code = genCode();
    const s = {
      code,
      nameA: nameA.value.trim(),
      nameB: nameB.value.trim(),
      answers: {},
      idx: 0,
      game: emptyGame(),
      ttt: emptyTtt(),
      chat: []
    };
    try{
      await saveSession(s);
      pendingSession = s;
      shareCode.textContent = code;
      shareTarget.textContent = s.nameB;
      shareBox.classList.add('show');
      createBtn.style.display = 'none';
    }catch(e){
      createBtn.disabled = false; createBtn.textContent = 'Créer notre espace';
      alert("Impossible de créer l'espace pour le moment. Réessaie.");
    }
  });

  copyBtn.addEventListener('click', () => {
    navigator.clipboard && navigator.clipboard.writeText(shareCode.textContent);
    copyBtn.textContent = 'Copié ✓';
    setTimeout(()=> copyBtn.textContent = 'Copier le code', 1500);
  });

  enterBtn.addEventListener('click', () => {
    session = pendingSession;
    myRole = 'a';
    local.set({code:session.code, role:'a'});
    boot();
  });

  const joinCode = document.getElementById('joinCode');
  const joinName = document.getElementById('joinName');
  const joinBtn = document.getElementById('joinBtn');
  const joinErr = document.getElementById('joinErr');

  function checkJoin(){ joinBtn.disabled = !(joinCode.value.trim() && joinName.value.trim()); }
  joinCode.addEventListener('input', checkJoin);
  joinName.addEventListener('input', checkJoin);
  joinCode.addEventListener('input', () => { joinCode.value = joinCode.value.toUpperCase(); });

  joinBtn.addEventListener('click', async () => {
    joinErr.textContent = '';
    joinBtn.disabled = true; joinBtn.textContent = 'Connexion…';
    const code = joinCode.value.trim().toUpperCase();
    try{
      const s = await loadSession(code);
      if(!s){
        joinErr.textContent = "Ce code ne correspond à aucun espace. Vérifie-le auprès de l'autre.";
        joinBtn.disabled = false; joinBtn.textContent = 'Rejoindre';
        return;
      }
      session = s;
      myRole = 'b';
      local.set({code:session.code, role:'b'});
      boot();
    }catch(e){
      joinErr.textContent = "Connexion impossible pour l'instant. Réessaie dans un instant.";
      joinBtn.disabled = false; joinBtn.textContent = 'Rejoindre';
    }
  });

  document.getElementById('resetBtn').addEventListener('click', () => {
    if(confirm("Quitter cet espace sur cet appareil ? Vos réponses restent enregistrées en ligne, vous pourrez revenir avec le même code.")){
      if(pollTimer) clearInterval(pollTimer);
      localStorage.removeItem(SS_KEY);
      location.reload();
    }
  });

  const setupEl = document.getElementById('setup');
  const mainEl = document.getElementById('main');

  function boot(){
    setupEl.style.display = 'none';
    mainEl.classList.add('active');
    document.getElementById('codeLabel').textContent = session.code;
    document.getElementById('nameLabelA').textContent = session.nameA;
    document.getElementById('nameLabelB').textContent = session.nameB;
    document.getElementById('labelA').textContent = session.nameA;
    document.getElementById('labelB').textContent = session.nameB;
    document.getElementById('diceNameA').textContent = session.nameA;
    document.getElementById('diceNameB').textContent = session.nameB;
    document.getElementById('truthNameLabelA').textContent = session.nameA;
    document.getElementById('truthNameLabelB').textContent = session.nameB;
    document.getElementById('tttSymbolNote').textContent =
      `${session.nameA} joue ❌ · ${session.nameB} joue ⭕ · ${session.nameA} commence toujours la partie. En 4×4 et 5×5, 4 pions alignés suffisent pour gagner.`;
    buildThemeNav();
    render();
    renderGame();
    renderTtt();
    lastGameSnapshot = JSON.stringify(session.game);
    lastAnswersSnapshot = JSON.stringify(session.answers);
    lastTttSnapshot = JSON.stringify(session.ttt);
    lastChatSnapshot = JSON.stringify(session.chat || []);
    chatReadCount = (session.chat || []).length;
    document.getElementById('chatSub').textContent =
      `Entre ${session.nameA} et ${session.nameB}`;
    initChatUi();
    renderChat(true);
    updateChatBadge();
    pollTimer = setInterval(syncFromRemote, 3000);
  }

  function buildThemeNav(){
    const nav = document.getElementById('themeNav');
    nav.innerHTML = '';
    THEMES.forEach(t => {
      const btn = document.createElement('button');
      btn.className = 'theme-pill';
      btn.textContent = t.label;
      btn.addEventListener('click', () => {
        if(pendingAnswerSave) return; // bonus solidité : pas de changement de thème pendant un envoi
        const firstIdx = QUESTIONS.findIndex(q => q.theme === t.key);
        if(firstIdx >= 0){ session.idx = firstIdx; render(); }
      });
      nav.appendChild(btn);
    });
  }

  function currentThemeKey(){ return QUESTIONS[session.idx].theme; }

  function refreshThemeNav(){
    const pills = document.querySelectorAll('.theme-pill');
    const cur = currentThemeKey();
    pills.forEach((p, i) => p.classList.toggle('active', THEMES[i].key === cur));
  }

  async function syncFromRemote(){
    if(localMutationBusy) return;
    try{
      const fresh = await loadSession(session.code);
      if(!fresh) return;
      if(localMutationBusy) return;

      const freshAnswersStr = JSON.stringify(fresh.answers);
      const freshGameStr = JSON.stringify(fresh.game || emptyGame());
      const freshTttStr = JSON.stringify(fresh.ttt || emptyTtt());
      const freshChatStr = JSON.stringify(fresh.chat || []);
      const answersChanged = freshAnswersStr !== lastAnswersSnapshot;
      const gameChanged = freshGameStr !== lastGameSnapshot;
      const tttChanged = freshTttStr !== lastTttSnapshot;
      const chatChanged = freshChatStr !== lastChatSnapshot;

      if(!answersChanged && !gameChanged && !tttChanged && !chatChanged) return;

      session.nameA = fresh.nameA;
      session.nameB = fresh.nameB;

      // Bonus solidité : si un envoi de réponse est en cours, on ne laisse pas
      // le polling toutes les 3s écraser le state pendant qu'on attend la
      // confirmation de patchAnswer() — sinon on pourrait re-perdre le fil.
      if(answersChanged && !pendingAnswerSave){
        session.answers = fresh.answers;
        lastAnswersSnapshot = freshAnswersStr;
        renderAnswersOnly();
      }
      if(gameChanged){
        session.game = fresh.game || emptyGame();
        lastGameSnapshot = freshGameStr;
        renderGame();
      }
      if(tttChanged){
        session.ttt = fresh.ttt || emptyTtt();
        lastTttSnapshot = freshTttStr;
        renderTtt();
      }
      if(chatChanged){
        session.chat = fresh.chat || [];
        lastChatSnapshot = freshChatStr;
        renderChat(chatOpen);
        updateChatBadge();
      }
    }catch(e){ /* silent retry next tick */ }
  }

  function formatChatTime(iso){
    if(!iso) return '';
    try{
      const d = new Date(iso);
      return d.toLocaleString('fr-FR', { day:'numeric', month:'short', hour:'2-digit', minute:'2-digit' });
    }catch(e){ return ''; }
  }

  function renderChat(scrollBottom){
    const box = document.getElementById('chatMessages');
    if(!box) return;
    const msgs = session.chat || [];
    if(!msgs.length){
      box.innerHTML = '<p class="chat-empty">Aucun message pour l\'instant. Lance la conversation — une réponse, un mot doux, une question…</p>';
      return;
    }
    box.innerHTML = '';
    msgs.forEach(m => {
      const mine = m.from === myRole;
      const wrap = document.createElement('div');
      wrap.className = 'chat-bubble ' + (mine ? 'mine' : 'theirs');
      const who = m.from === 'a' ? session.nameA : session.nameB;
      wrap.innerHTML = `<div class="chat-meta">${escapeHtml(who)} · ${formatChatTime(m.at)}</div>${escapeHtml(m.text)}`;
      box.appendChild(wrap);
    });
    if(scrollBottom || chatOpen){
      box.scrollTop = box.scrollHeight;
      if(chatOpen) chatReadCount = msgs.length;
      updateChatBadge();
    }
  }

  function escapeHtml(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function updateChatBadge(){
    const badge = document.getElementById('chatBadge');
    if(!badge) return;
    const unread = Math.max(0, (session.chat || []).length - chatReadCount);
    if(unread > 0 && !chatOpen){
      badge.textContent = unread > 9 ? '9+' : String(unread);
      badge.classList.add('show');
    }else{
      badge.classList.remove('show');
    }
  }

  async function sendChatMessage(){
    const input = document.getElementById('chatInput');
    const text = (input.value || '').trim();
    if(!text || chatSending) return;
    chatSending = true;
    document.getElementById('chatSendBtn').disabled = true;
    try{
      const res = await fetch(`${API}/${encodeURIComponent(session.code)}/chat`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ from: myRole, text })
      });
      if(!res.ok) throw new Error('chat failed');
      const s = await res.json();
      session.chat = s.chat || [];
      lastChatSnapshot = JSON.stringify(session.chat);
      input.value = '';
      renderChat(true);
    }catch(e){
      alert("Message impossible à envoyer pour l'instant. Réessaie.");
    }finally{
      chatSending = false;
      document.getElementById('chatSendBtn').disabled = false;
    }
  }

  function openChat(){
    chatOpen = true;
    const ov = document.getElementById('chatOverlay');
    ov.classList.add('open');
    ov.setAttribute('aria-hidden', 'false');
    chatReadCount = (session.chat || []).length;
    updateChatBadge();
    renderChat(true);
    setTimeout(() => document.getElementById('chatInput')?.focus(), 120);
  }

  function closeChat(){
    chatOpen = false;
    const ov = document.getElementById('chatOverlay');
    ov.classList.remove('open');
    ov.setAttribute('aria-hidden', 'true');
  }

  function initChatUi(){
    document.getElementById('openChatBtn').addEventListener('click', openChat);
    document.getElementById('closeChatBtn').addEventListener('click', closeChat);
    document.getElementById('chatOverlay').addEventListener('click', e => {
      if(e.target.id === 'chatOverlay') closeChat();
    });
    document.getElementById('chatSendBtn').addEventListener('click', sendChatMessage);
    document.getElementById('chatInput').addEventListener('keydown', e => {
      if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); sendChatMessage(); }
    });
  }

  function answeredCount(){
    let n = 0;
    for(const k in session.answers){
      const a = session.answers[k];
      if(a && a.a && a.a.trim() && a.b && a.b.trim()) n++;
    }
    return n;
  }

  function updateThread(){
    const total = QUESTIONS.length;
    const done = answeredCount();
    const pct = done/total;
    const x = 6 + pct*388;
    document.getElementById('threadFill').setAttribute('x2', x);
    document.getElementById('threadDot').setAttribute('cx', x);
  }

  function render(){
    if(pendingAnswerSave) return; // bonus solidité : pas de changement de question pendant un envoi
    const i = session.idx;
    const q = QUESTIONS[i];
    const card = document.getElementById('qCard');
    card.classList.remove('fade'); void card.offsetWidth; card.classList.add('fade');

    document.getElementById('qNumber').textContent = `${q.label} · Question ${i+1} sur ${QUESTIONS.length}`;
    document.getElementById('qText').textContent = q.text;
    const tag = document.getElementById('catTag');
    tag.textContent = q.label;
    tag.style.color = THEMES.find(t=>t.key===q.theme).color;

    refreshThemeNav();
    renderAnswersOnly();

    document.getElementById('prevBtn').disabled = i === 0;
    document.getElementById('nextBtn').textContent = i === QUESTIONS.length-1 ? 'terminer' : 'suivante →';

    updateThread();
  }

  function renderAnswersOnly(){
    const i = session.idx;
    const rec = session.answers[i] || {a:'', b:''};
    const bothDone = rec.a && rec.a.trim() && rec.b && rec.b.trim();

    renderSlot('A', 'a', rec, bothDone);
    renderSlot('B', 'b', rec, bothDone);

    document.getElementById('statusA').textContent = rec.a && rec.a.trim() ? '✓ répondu' : '';
    document.getElementById('statusA').classList.toggle('done', !!(rec.a && rec.a.trim()));
    document.getElementById('statusB').textContent = rec.b && rec.b.trim() ? '✓ répondu' : '';
    document.getElementById('statusB').classList.toggle('done', !!(rec.b && rec.b.trim()));

    document.getElementById('progressLabel').textContent = `${answeredCount()} / ${QUESTIONS.length} partagées`;
    updateThread();
  }

  function renderSlot(letter, key, rec, bothDone){
    const slot = document.getElementById('slot'+letter);
    const mine = rec[key] || '';
    const isMine = key === myRole;
    const i = session.idx;
    const isEditing = editingQuestions.has(i);

    // Correctif : #slotA/#slotB sont des éléments DOM FIXES, réutilisés pour
    // toutes les questions. On marque à quelle question le <textarea> actuel
    // appartient (data-qidx), et on ne le préserve QUE si on est encore sur
    // cette même question. Sans ça, passer d'une question sans réponse (ex. Q2)
    // à une autre sans réponse (ex. Q3, Q4...) laissait l'ancien <textarea> en
    // place — avec son bouton "Valider" toujours câblé sur l'ancien index —
    // ce qui faisait enregistrer la réponse sur la mauvaise question.
    const existingTa = slot.querySelector('textarea');
    const sameQuestionAsExisting = existingTa && slot.dataset.qidx === String(i);

    if(!mine.trim() && isMine && sameQuestionAsExisting){
      return;
    }
    if(mine.trim() && isMine && isEditing && sameQuestionAsExisting){
      return;
    }

    slot.dataset.qidx = String(i);
    slot.innerHTML = '';

    if(mine.trim()){
      if(isMine && isEditing){
        buildAnswerInput(slot, mine, key, i, true);
        return;
      }
      const p = document.createElement('div');
      p.className = 'answer-locked';
      p.textContent = mine;
      slot.appendChild(p);

      if(isMine){
        if(!bothDone){
          const veil = document.createElement('div');
          veil.className = 'veil';
          veil.textContent = `En attente de ${key==='a'?session.nameB:session.nameA}…`;
          slot.appendChild(veil);
        }
        const editBtn = document.createElement('button');
        editBtn.className = 'edit-btn';
        editBtn.textContent = 'Modifier ma réponse ✎';
        editBtn.addEventListener('click', () => {
          if(pendingAnswerSave) return;
          editingQuestions.add(i);
          renderAnswersOnly();
        });
        slot.appendChild(editBtn);
      } else if(!bothDone){
        const veil = document.createElement('div');
        veil.className = 'veil';
        veil.textContent = `Réponse cachée jusqu'à ce que tu répondes aussi.`;
        slot.appendChild(veil);
      }
      return;
    }

    if(!isMine){
      const veil = document.createElement('div');
      veil.className = 'veil';
      veil.style.marginTop = '0';
      veil.textContent = `En attente de la réponse de ${key==='a'?session.nameA:session.nameB}…`;
      slot.appendChild(veil);
      return;
    }

    buildAnswerInput(slot, mine, key, i, false);
  }

  // Boutons de navigation, pour pouvoir les verrouiller pendant un envoi (bonus solidité).
  const prevBtnEl = document.getElementById('prevBtn');
  const nextBtnEl = document.getElementById('nextBtn');

  function setNavLocked(locked){
    // On respecte quand même la limite de bornes (première/dernière question)
    // une fois déverrouillé, via un simple ré-appel de render().
    prevBtnEl.disabled = locked ? true : (session.idx === 0);
    nextBtnEl.disabled = locked ? true : false;
    document.querySelectorAll('.theme-pill').forEach(p => p.style.pointerEvents = locked ? 'none' : '');
    document.querySelectorAll('.theme-pill').forEach(p => p.style.opacity = locked ? '0.5' : '');
  }

  function buildAnswerInput(slot, mine, key, i, isEdit){
    const ta = document.createElement('textarea');
    ta.rows = 5;
    ta.placeholder = 'Écris ta réponse ici…';
    ta.value = mine;
    slot.appendChild(ta);

    const row = document.createElement('div');
    row.style.display = 'flex';
    row.style.gap = '8px';
    row.style.flexWrap = 'wrap';

    const btn = document.createElement('button');
    btn.className = 'save-btn';
    btn.textContent = isEdit ? 'Enregistrer la modification' : 'Valider ma réponse';
    btn.addEventListener('click', async () => {
      const val = ta.value.trim();
      if(!val) return;

      // On fige l'indice de la question ET la clé au moment du clic : c'est
      // essentiel, car session.idx peut changer avant la fin de l'envoi si
      // l'utilisateur navigue (d'où le bug "ça part sur la question 3").
      const savedForIdx = i;
      const savedForKey = key;

      btn.disabled = true; btn.textContent = 'Envoi…';
      pendingAnswerSave = true;
      setNavLocked(true); // bonus solidité : on bloque prev/next/thèmes pendant l'envoi

      try{
        // Patch atomique côté serveur : ne touche que (savedForIdx, savedForKey),
        // ne peut donc jamais écraser une réponse que l'autre personne vient
        // de valider entre-temps.
        const mergedAnswers = await patchAnswer(savedForIdx, savedForKey, val);

        session.answers = mergedAnswers;
        lastAnswersSnapshot = JSON.stringify(session.answers);
        editingQuestions.delete(savedForIdx);

        // On ne touche au DOM que si on est encore sur la question concernée.
        if(session.idx === savedForIdx){
          renderAnswersOnly();
        } else {
          updateThread();
        }
      }catch(e){
        if(session.idx === savedForIdx){
          btn.disabled = false; btn.textContent = isEdit ? 'Enregistrer la modification' : 'Valider ma réponse';
        }
        alert("Envoi impossible pour le moment, réessaie.");
      }finally{
        pendingAnswerSave = false;
        setNavLocked(false);
      }
    });
    row.appendChild(btn);

    if(isEdit){
      const cancelBtn = document.createElement('button');
      cancelBtn.className = 'edit-btn';
      cancelBtn.textContent = 'Annuler';
      cancelBtn.addEventListener('click', () => {
        if(pendingAnswerSave) return;
        editingQuestions.delete(i);
        renderAnswersOnly();
      });
      row.appendChild(cancelBtn);
    }

    slot.appendChild(row);
  }

  prevBtnEl.addEventListener('click', () => {
    if(pendingAnswerSave) return; // bonus solidité
    if(session.idx > 0){ session.idx--; render(); }
  });
  nextBtnEl.addEventListener('click', () => {
    if(pendingAnswerSave) return; // bonus solidité
    if(session.idx < QUESTIONS.length-1){ session.idx++; render(); }
    else { alert("Vous avez parcouru toutes les questions. Vous pouvez revenir en arrière pour relire vos réponses."); }
  });

  const modeQuestionsBtn = document.getElementById('modeQuestionsBtn');
  const modeGameBtn = document.getElementById('modeGameBtn');
  const modeTttBtn = document.getElementById('modeTttBtn');
  const questionsView = document.getElementById('questionsView');
  const gameView = document.getElementById('gameView');
  const tttView = document.getElementById('tttView');

  function activateAppMode(which){
    modeQuestionsBtn.classList.toggle('active', which === 'questions');
    modeGameBtn.classList.toggle('active', which === 'game');
    modeTttBtn.classList.toggle('active', which === 'ttt');
    questionsView.style.display = which === 'questions' ? '' : 'none';
    gameView.style.display = which === 'game' ? '' : 'none';
    tttView.style.display = which === 'ttt' ? '' : 'none';
  }

  modeQuestionsBtn.addEventListener('click', () => { if(!pendingAnswerSave) activateAppMode('questions'); });
  modeGameBtn.addEventListener('click', () => { if(!pendingAnswerSave) activateAppMode('game'); });
  modeTttBtn.addEventListener('click', () => { if(!pendingAnswerSave) activateAppMode('ttt'); });

  const rollBtnA = document.getElementById('rollBtnA');
  const rollBtnB = document.getElementById('rollBtnB');
  const diceFaceA = document.getElementById('diceFaceA');
  const diceFaceB = document.getElementById('diceFaceB');
  const diceWaitA = document.getElementById('diceWaitA');
  const diceWaitB = document.getElementById('diceWaitB');
  const diceResult = document.getElementById('diceResult');
  const choiceRow = document.getElementById('choiceRow');
  const chooseTruth = document.getElementById('chooseTruth');
  const chooseAction = document.getElementById('chooseAction');
  const promptCard = document.getElementById('promptCard');
  const promptLabel = document.getElementById('promptLabel');
  const promptText = document.getElementById('promptText');
  const truthAnswersWrap = document.getElementById('truthAnswersWrap');
  const preConfirmRow = document.getElementById('preConfirmRow');
  const promptNavRow = document.getElementById('promptNavRow');
  const rerollPrompt = document.getElementById('rerollPrompt');
  const confirmDoneBtn = document.getElementById('confirmDoneBtn');
  const confirmWaitLabel = document.getElementById('confirmWaitLabel');
  const newRoundBtn = document.getElementById('newRoundBtn');
  const modeSoftBtn = document.getElementById('modeSoftBtn');
  const modeHardBtn = document.getElementById('modeHardBtn');

  const DICE_FACES = ['⚀','⚁','⚂','⚃','⚄','⚅'];

  function loserOf(g){
    if(g.diceA == null || g.diceB == null) return null;
    if(g.diceA === g.diceB) return 'tie';
    return g.diceA < g.diceB ? 'a' : 'b';
  }

  async function patchGame(patch){
    localMutationBusy = true;
    try{
      const res = await fetch(`${API}/${encodeURIComponent(session.code)}/game`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ patch })
      });
      if(!res.ok) throw new Error('patch failed');
      const s = await res.json();
      session.game = s.game || emptyGame();
      lastGameSnapshot = JSON.stringify(session.game);
      renderGame();
    }catch(e){
      alert("Action impossible pour le moment, réessaie.");
    }finally{
      localMutationBusy = false;
    }
  }

  async function patchTtt(patch){
    localMutationBusy = true;
    try{
      const res = await fetch(`${API}/${encodeURIComponent(session.code)}/ttt`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ patch })
      });
      if(!res.ok) throw new Error('patch failed');
      const s = await res.json();
      session.ttt = s.ttt || emptyTtt();
      lastTttSnapshot = JSON.stringify(session.ttt);
      renderTtt();
    }catch(e){
      alert("Action impossible pour le moment, réessaie.");
    }finally{
      localMutationBusy = false;
    }
  }

  function animateRoll(faceEl, finalValue, onDone){
    faceEl.classList.add('rolling');
    let ticks = 0;
    const anim = setInterval(() => {
      faceEl.textContent = DICE_FACES[Math.floor(Math.random()*6)];
      ticks++;
      if(ticks > 9){
        clearInterval(anim);
        faceEl.classList.remove('rolling');
        faceEl.textContent = DICE_FACES[finalValue-1];
        if(onDone) onDone();
      }
    }, 90);
  }

  async function rollMine(roleKey){
    const btn = roleKey === 'a' ? rollBtnA : rollBtnB;
    const face = roleKey === 'a' ? diceFaceA : diceFaceB;
    btn.disabled = true;
    localMutationBusy = true;
    const val = 1 + Math.floor(Math.random()*6);
    animateRoll(face, val, async () => {
      await patchGame({ [roleKey === 'a' ? 'diceA' : 'diceB']: val });
    });
  }

  rollBtnA.addEventListener('click', () => { if(myRole === 'a') rollMine('a'); });
  rollBtnB.addEventListener('click', () => { if(myRole === 'b') rollMine('b'); });

  modeSoftBtn.addEventListener('click', () => { patchGame({ mode: 'soft' }); });
  modeHardBtn.addEventListener('click', () => { patchGame({ mode: 'hard' }); });

  chooseTruth.addEventListener('click', () => pickPrompt('truth'));
  chooseAction.addEventListener('click', () => pickPrompt('action'));

  function pickPrompt(type){
    const mode = (session.game && session.game.mode) || 'soft';
    let picked;
    if(type === 'truth'){
      if(mode === 'hard'){ picked = pickRandom(TRUTHS_HARD, usedTruthsHard); usedTruthsHard = picked.used; }
      else{ picked = pickRandom(TRUTHS_SOFT, usedTruthsSoft); usedTruthsSoft = picked.used; }
    } else {
      if(mode === 'hard'){ picked = pickRandom(ACTIONS_HARD, usedActionsHard); usedActionsHard = picked.used; }
      else{ picked = pickRandom(ACTIONS_SOFT, usedActionsSoft); usedActionsSoft = picked.used; }
    }
    editingTruth.clear();
    patchGame({ choice: type, promptType: type, promptText: picked.text, confirmed: false, truthAnswers:{a:'', b:''} });
  }

  rerollPrompt.addEventListener('click', () => {
    const g = session.game;
    if(g && g.promptType && !g.confirmed) pickPrompt(g.promptType);
  });

  confirmDoneBtn.addEventListener('click', () => {
    patchGame({ confirmed: true });
  });

  newRoundBtn.addEventListener('click', () => {
    const g = session.game;
    if(!g || !g.confirmed) return;
    patchGame({
      round: (g.round||0) + 1,
      diceA: null, diceB: null,
      choice: null, promptType: null, promptText: null, confirmed: false, truthAnswers:{a:'', b:''}
    });
  });

  const editingTruth = new Set();

  async function saveTruthAnswer(key, val, btn, isEdit){
    btn.disabled = true; btn.textContent = 'Envoi…';
    const current = (session.game && session.game.truthAnswers) || {a:'', b:''};
    const updated = { a: current.a || '', b: current.b || '' };
    updated[key] = val;
    await patchGame({ truthAnswers: updated });
    editingTruth.delete(key);
    btn.disabled = false; btn.textContent = isEdit ? 'Enregistrer la modification' : 'Valider ma réponse';
  }

  function buildTruthInput(slot, mine, key, isEdit){
    const taEl = document.createElement('textarea');
    taEl.rows = 4;
    taEl.placeholder = 'Ta réponse à cette vérité…';
    taEl.value = mine;
    slot.appendChild(taEl);

    const row = document.createElement('div');
    row.style.display = 'flex';
    row.style.gap = '8px';
    row.style.flexWrap = 'wrap';

    const btn = document.createElement('button');
    btn.className = 'save-btn';
    btn.textContent = isEdit ? 'Enregistrer la modification' : 'Valider ma réponse';
    btn.addEventListener('click', () => {
      const val = taEl.value.trim();
      if(!val) return;
      saveTruthAnswer(key, val, btn, isEdit);
    });
    row.appendChild(btn);

    if(isEdit){
      const cancelBtn = document.createElement('button');
      cancelBtn.className = 'edit-btn';
      cancelBtn.textContent = 'Annuler';
      cancelBtn.addEventListener('click', () => {
        editingTruth.delete(key);
        renderGame();
      });
      row.appendChild(cancelBtn);
    }

    slot.appendChild(row);
  }

  function renderTruthSlot(letter, key, ta_obj, bothTruthDone){
    const slot = document.getElementById('truthSlot'+letter);
    const mine = (ta_obj && ta_obj[key]) || '';
    const isMine = key === myRole;
    const isEditing = editingTruth.has(key);

    // Même correctif que renderSlot() : truthSlotA/truthSlotB sont réutilisés
    // à chaque nouveau prompt (nouveau tour, "une autre →"...). On identifie
    // le prompt actuel par son texte pour ne préserver le <textarea> que s'il
    // appartient bien au prompt affiché.
    const currentPromptKey = (session.game && session.game.promptText) || '';
    const existingTa = slot.querySelector('textarea');
    const samePromptAsExisting = existingTa && slot.dataset.promptKey === currentPromptKey;

    if(!mine.trim() && isMine && samePromptAsExisting){
      return;
    }
    if(mine.trim() && isMine && isEditing && samePromptAsExisting){
      return;
    }

    slot.dataset.promptKey = currentPromptKey;
    slot.innerHTML = '';

    if(mine.trim()){
      if(isMine && isEditing){
        buildTruthInput(slot, mine, key, true);
        return;
      }
      const p = document.createElement('div');
      p.className = 'answer-locked';
      p.textContent = mine;
      slot.appendChild(p);

      if(isMine){
        if(!bothTruthDone){
          const veil = document.createElement('div');
          veil.className = 'veil';
          veil.textContent = `En attente de ${key==='a'?session.nameB:session.nameA}…`;
          slot.appendChild(veil);
        }
        const editBtn = document.createElement('button');
        editBtn.className = 'edit-btn';
        editBtn.textContent = 'Modifier ma réponse ✎';
        editBtn.addEventListener('click', () => {
          editingTruth.add(key);
          renderGame();
        });
        slot.appendChild(editBtn);
      } else if(!bothTruthDone){
        const veil = document.createElement('div');
        veil.className = 'veil';
        veil.textContent = `Réponse cachée jusqu'à ce que tu répondes aussi.`;
        slot.appendChild(veil);
      }
      return;
    }

    if(!isMine){
      const veil = document.createElement('div');
      veil.className = 'veil';
      veil.style.marginTop = '0';
      veil.textContent = `En attente de la réponse de ${key==='a'?session.nameA:session.nameB}…`;
      slot.appendChild(veil);
      return;
    }

    buildTruthInput(slot, mine, key, false);
  }

  function renderGame(){
    if(!session) return;
    const g = session.game || emptyGame();
    const mode = g.mode || 'soft';

    modeSoftBtn.classList.toggle('active', mode !== 'hard');
    modeHardBtn.classList.toggle('active', mode === 'hard');
    const roundInProgress = g.diceA != null || g.diceB != null;
    modeSoftBtn.disabled = roundInProgress;
    modeHardBtn.disabled = roundInProgress;

    diceFaceA.textContent = g.diceA != null ? DICE_FACES[g.diceA-1] : '🎲';
    diceFaceB.textContent = g.diceB != null ? DICE_FACES[g.diceB-1] : '🎲';

    const myDice = myRole === 'a' ? g.diceA : g.diceB;
    const myBtn = myRole === 'a' ? rollBtnA : rollBtnB;
    const otherBtn = myRole === 'a' ? rollBtnB : rollBtnA;
    myBtn.disabled = myDice != null;
    otherBtn.disabled = true;

    diceWaitA.textContent = g.diceA == null ? (myRole==='a' ? '' : `en attente de ${session.nameA}…`) : '';
    diceWaitB.textContent = g.diceB == null ? (myRole==='b' ? '' : `en attente de ${session.nameB}…`) : '';

    const loser = loserOf(g);
    confirmWaitLabel.textContent = '';

    if(loser === 'tie'){
      diceResult.textContent = `Égalité (${g.diceA} - ${g.diceA}) — relancez les dés !`;
      choiceRow.style.display = 'none';
      promptCard.style.display = 'none';
      preConfirmRow.style.display = 'none';
      promptNavRow.style.display = 'none';
      return;
    }

    if(!loser){
      diceResult.textContent = '';
      choiceRow.style.display = 'none';
      promptCard.style.display = 'none';
      preConfirmRow.style.display = 'none';
      promptNavRow.style.display = 'none';
      return;
    }

    const loserName = loser === 'a' ? session.nameA : session.nameB;
    diceResult.textContent = `${loserName} a le score le plus bas (${Math.min(g.diceA,g.diceB)} contre ${Math.max(g.diceA,g.diceB)}) — action ou vérité ?`;

    if(!g.promptType){
      if(myRole === loser){
        choiceRow.style.display = 'flex';
      } else {
        choiceRow.style.display = 'none';
        diceResult.textContent += ` En attente du choix de ${loserName}…`;
      }
      promptCard.style.display = 'none';
      preConfirmRow.style.display = 'none';
      promptNavRow.style.display = 'none';
      return;
    }

    choiceRow.style.display = 'none';
    promptLabel.textContent = (g.promptType === 'truth' ? 'Vérité' : 'Action') + ' · ' + (mode === 'hard' ? 'Hard 🔥' : 'Soft');
    promptText.textContent = g.promptText;
    promptCard.classList.remove('fade'); void promptCard.offsetWidth; promptCard.classList.add('fade');
    promptCard.style.display = 'block';

    const isTruth = g.promptType === 'truth';
    let bothTruthDone = false;

    if(isTruth){
      truthAnswersWrap.style.display = 'block';
      const ta = g.truthAnswers || {a:'', b:''};
      bothTruthDone = !!(ta.a && ta.a.trim() && ta.b && ta.b.trim());
      renderTruthSlot('A', 'a', ta, bothTruthDone);
      renderTruthSlot('B', 'b', ta, bothTruthDone);
      document.getElementById('truthStatusA').textContent = ta.a && ta.a.trim() ? '✓ répondu' : '';
      document.getElementById('truthStatusA').classList.toggle('done', !!(ta.a && ta.a.trim()));
      document.getElementById('truthStatusB').textContent = ta.b && ta.b.trim() ? '✓ répondu' : '';
      document.getElementById('truthStatusB').classList.toggle('done', !!(ta.b && ta.b.trim()));
    } else {
      truthAnswersWrap.style.display = 'none';
    }

    const readyToConfirm = isTruth ? bothTruthDone : true;

    if(!g.confirmed){
      promptNavRow.style.display = 'none';
      if(!readyToConfirm){
        preConfirmRow.style.display = 'none';
        confirmWaitLabel.textContent = `En attente que ${session.nameA} et ${session.nameB} répondent tous les deux…`;
      } else if(myRole === loser){
        preConfirmRow.style.display = 'flex';
      } else {
        preConfirmRow.style.display = 'none';
        confirmWaitLabel.textContent = `En attente que ${loserName} confirme avoir terminé…`;
      }
    } else {
      preConfirmRow.style.display = 'none';
      promptNavRow.style.display = 'flex';
      confirmWaitLabel.textContent = 'Tour terminé — prêt·e pour la suite.';
    }
  }

  // ---------- Morpion (synchronisé, taille variable 3×3 / 4×4 / 5×5) ----------
  const tttBoardEl = document.getElementById('tttBoard');
  const tttTurnEl = document.getElementById('tttTurn');
  const tttScoreEl = document.getElementById('tttScore');
  const tttSeriesEl = document.getElementById('tttSeries');
  const tttStealRowEl = document.getElementById('tttStealRow');
  const tttToastEl = document.getElementById('tttToast');
  const tttTimerEl = document.getElementById('tttTimer');
  const tttWinnerChoiceEl = document.getElementById('tttWinnerChoice');
  const tttWinnerChoiceLabelEl = document.getElementById('tttWinnerChoiceLabel');
  const tttPickThemeBtn = document.getElementById('tttPickThemeBtn');
  const tttPickHardBtn = document.getElementById('tttPickHardBtn');
  const tttPickSoftBtn = document.getElementById('tttPickSoftBtn');
  const tttResetBtn = document.getElementById('tttResetBtn');
  const tttSize3Btn = document.getElementById('tttSize3Btn');
  const tttSize4Btn = document.getElementById('tttSize4Btn');
  const tttSize5Btn = document.getElementById('tttSize5Btn');
  const tttBo3Btn = document.getElementById('tttBo3Btn');
  const tttBo5Btn = document.getElementById('tttBo5Btn');
  const tttNormalBtn = document.getElementById('tttNormalBtn');
  const tttSuddenBtn = document.getElementById('tttSuddenBtn');

  function winLength(n, sudden){
    if(sudden) return 3;
    return n <= 3 ? 3 : 4;
  }

  function generateAllLines(n, k){
    const lines = [];
    for(let r=0; r<n; r++){
      const row = []; for(let c=0; c<n; c++) row.push(r*n+c);
      lines.push(row);
    }
    for(let c=0; c<n; c++){
      const col = []; for(let r=0; r<n; r++) col.push(r*n+c);
      lines.push(col);
    }
    for(let d = -(n-k); d <= (n-k); d++){
      const diag = [];
      for(let r=0; r<n; r++){
        const c = r + d;
        if(c >= 0 && c < n) diag.push(r*n+c);
      }
      if(diag.length >= k) lines.push(diag);
    }
    for(let s = (k-1); s <= (2*n-k); s++){
      const diag = [];
      for(let r=0; r<n; r++){
        const c = s - r;
        if(c >= 0 && c < n) diag.push(r*n+c);
      }
      if(diag.length >= k) lines.push(diag);
    }
    return lines;
  }

  function checkTttWinner(board, n, sudden){
    const k = winLength(n, sudden);
    const lines = generateAllLines(n, k);
    for(const line of lines){
      for(let i=0; i + k <= line.length; i++){
        const window = line.slice(i, i+k);
        const first = board[window[0]];
        if(first && window.every(idx => board[idx] === first)){
          return { winner:first, line:window };
        }
      }
    }
    if(board.every(cell => cell)) return { winner:'tie', line:null };
    return null;
  }

  function buildTttBoard(n){
    tttBoardEl.innerHTML = '';
    tttBoardEl.style.gridTemplateColumns = `repeat(${n}, 1fr)`;
    const fontSize = n === 3 ? 'clamp(28px,8vw,40px)' : n === 4 ? 'clamp(20px,6vw,30px)' : 'clamp(16px,4.5vw,24px)';
    for(let i=0;i<n*n;i++){
      const cell = document.createElement('button');
      cell.className = 'ttt-cell';
      cell.style.fontSize = fontSize;
      cell.dataset.idx = i;
      cell.addEventListener('click', () => onTttCellClick(i));
      tttBoardEl.appendChild(cell);
    }
  }

  const STEAL_CHANCE = 0.22;

  function seriesTarget(bestOf){ return Math.ceil((bestOf||3)/2); }

  async function onTttCellClick(i){
    const t = session.ttt || emptyTtt();
    if(t.winner) return;
    if(t.seriesWinner) return;
    if(t.turn !== myRole) return;
    if(t.board[i]) return;

    const n = t.size || 3;
    const board = t.board.slice();
    const mySymbol = myRole === 'a' ? 'X' : 'O';
    board[i] = mySymbol;
    const result = checkTttWinner(board, n, t.sudden);
    const nextTurn = myRole === 'a' ? 'b' : 'a';
    const patch = { board, turn: nextTurn, lastToast: null };

    if(!result && Math.random() < STEAL_CHANCE){
      if(myRole === 'a') patch.stealsA = (t.stealsA||0) + 1;
      else patch.stealsB = (t.stealsB||0) + 1;
      patch.lastToast = `${myRole==='a'?session.nameA:session.nameB} gagne une charge de vol 🔓 !`;
    }

    if(result){
      patch.winner = result.winner;
      patch.line = result.line;
      let seriesA = t.seriesA||0, seriesB = t.seriesB||0;
      if(result.winner === 'X'){ patch.scoreA = (t.scoreA||0) + 1; seriesA++; }
      else if(result.winner === 'O'){ patch.scoreB = (t.scoreB||0) + 1; seriesB++; }
      else { patch.scoreTie = (t.scoreTie||0) + 1; }
      patch.seriesA = seriesA;
      patch.seriesB = seriesB;
      const target = seriesTarget(t.bestOf);
      if(seriesA >= target || seriesB >= target){
        patch.seriesWinner = seriesA >= target ? 'a' : 'b';
      }
    }
    await patchTtt(patch);
  }

  async function stealTurn(){
    const t = session.ttt || emptyTtt();
    if(t.winner || t.seriesWinner) return;
    const myCharges = myRole === 'a' ? (t.stealsA||0) : (t.stealsB||0);
    if(myCharges <= 0) return;
    if(t.turn === myRole) return;
    const patch = { turn: myRole, lastToast: `${myRole==='a'?session.nameA:session.nameB} vole le tour ! 🕵️` };
    if(myRole === 'a') patch.stealsA = myCharges - 1;
    else patch.stealsB = myCharges - 1;
    await patchTtt(patch);
  }

  tttResetBtn.addEventListener('click', () => {
    const t = session.ttt || emptyTtt();
    const n = t.size || 3;
    patchTtt({
      board: Array(n*n).fill(null), winner:null, line:null, turn: 'a',
      scoreA:0, scoreB:0, scoreTie:0,
      seriesA:0, seriesB:0, seriesWinner:null, seriesRound:1,
      stealsA:0, stealsB:0, lastToast:null, winnerChoiceDone:false
    });
  });

  function changeTttSize(newSize){
    const t = session.ttt || emptyTtt();
    if(t.board.some(c=>c) || t.seriesA || t.seriesB) return;
    patchTtt({
      size: newSize,
      board: Array(newSize*newSize).fill(null),
      winner:null, line:null, turn:'a',
      scoreA:0, scoreB:0, scoreTie:0
    });
  }
  tttSize3Btn.addEventListener('click', () => changeTttSize(3));
  tttSize4Btn.addEventListener('click', () => changeTttSize(4));
  tttSize5Btn.addEventListener('click', () => changeTttSize(5));

  function changeBestOf(n){
    const t = session.ttt || emptyTtt();
    if(t.seriesA || t.seriesB) return;
    patchTtt({ bestOf:n });
  }
  tttBo3Btn.addEventListener('click', () => changeBestOf(3));
  tttBo5Btn.addEventListener('click', () => changeBestOf(5));

  function toggleSudden(on){
    const t = session.ttt || emptyTtt();
    if(t.board.some(c=>c)) return;
    patchTtt({ sudden: on });
  }
  tttNormalBtn.addEventListener('click', () => toggleSudden(false));
  tttSuddenBtn.addEventListener('click', () => toggleSudden(true));

  tttPickThemeBtn.addEventListener('click', () => {
    activateAppMode('questions');
    patchTtt({ winnerChoiceDone:true });
  });
  tttPickHardBtn.addEventListener('click', () => {
    patchGame({ mode:'hard' });
    patchTtt({ winnerChoiceDone:true });
  });
  tttPickSoftBtn.addEventListener('click', () => {
    patchGame({ mode:'soft' });
    patchTtt({ winnerChoiceDone:true });
  });

  let lastBuiltTttSize = null;
  let suddenTimerHandle = null;
  let suddenTimerDeadline = null;
  const SUDDEN_MOVE_SECONDS = 8;

  function clearSuddenTimer(){
    if(suddenTimerHandle){ clearInterval(suddenTimerHandle); suddenTimerHandle = null; }
    suddenTimerDeadline = null;
    tttTimerEl.textContent = '';
  }

  function startSuddenTimerIfNeeded(t){
    if(!t.sudden || t.winner || t.seriesWinner){ clearSuddenTimer(); return; }
    if(t.turn !== myRole){ clearSuddenTimer(); return; }
    if(suddenTimerHandle) return;
    suddenTimerDeadline = Date.now() + SUDDEN_MOVE_SECONDS*1000;
    suddenTimerHandle = setInterval(() => {
      const remaining = Math.max(0, Math.ceil((suddenTimerDeadline - Date.now())/1000));
      tttTimerEl.textContent = `⚡ ${remaining}s pour jouer`;
      if(remaining <= 0){
        clearSuddenTimer();
        const cur = session.ttt || emptyTtt();
        if(!cur.winner && !cur.seriesWinner && cur.turn === myRole){
          patchTtt({ turn: myRole === 'a' ? 'b' : 'a', lastToast: `${myRole==='a'?session.nameA:session.nameB} a manqué de temps ⏱️` });
        }
      }
    }, 250);
  }

  function renderTtt(){
    if(!session) return;
    const t = session.ttt || emptyTtt();
    const n = t.size || 3;

    if(lastBuiltTttSize !== n){
      buildTttBoard(n);
      lastBuiltTttSize = n;
    }

    const inProgress = t.board.some(c => c);
    const seriesStarted = (t.seriesA||0) > 0 || (t.seriesB||0) > 0;

    tttSize3Btn.classList.toggle('active', n === 3);
    tttSize4Btn.classList.toggle('active', n === 4);
    tttSize5Btn.classList.toggle('active', n === 5);
    tttSize3Btn.disabled = inProgress || seriesStarted;
    tttSize4Btn.disabled = inProgress || seriesStarted;
    tttSize5Btn.disabled = inProgress || seriesStarted;

    const bestOf = t.bestOf || 3;
    tttBo3Btn.classList.toggle('active', bestOf === 3);
    tttBo5Btn.classList.toggle('active', bestOf === 5);
    tttBo3Btn.disabled = seriesStarted;
    tttBo5Btn.disabled = seriesStarted;

    tttNormalBtn.classList.toggle('active', !t.sudden);
    tttSuddenBtn.classList.toggle('active', !!t.sudden);
    tttNormalBtn.disabled = inProgress;
    tttSuddenBtn.disabled = inProgress;

    const target = seriesTarget(bestOf);
    tttSeriesEl.innerHTML = `Série (Bo${bestOf}) — manche ${(t.seriesA||0)+(t.seriesB||0)+1} · il faut <strong>${target}</strong> victoire(s)`;

    tttScoreEl.innerHTML = `<strong>${session.nameA}</strong> (❌) ${t.seriesA||0} — ${t.seriesB||0} <strong>${session.nameB}</strong> (⭕) · ${t.scoreTie||0} égalité(s) au total`;

    tttStealRowEl.innerHTML = '';
    const chipA = document.createElement('div');
    chipA.className = 'ttt-steal-chip' + (myRole==='a' ? ' mine' : '');
    chipA.textContent = `${session.nameA} : ${t.stealsA||0} vol(s) 🔓`;
    tttStealRowEl.appendChild(chipA);
    const chipB = document.createElement('div');
    chipB.className = 'ttt-steal-chip' + (myRole==='b' ? ' mine' : '');
    chipB.textContent = `${session.nameB} : ${t.stealsB||0} vol(s) 🔓`;
    tttStealRowEl.appendChild(chipB);

    const myCharges = myRole === 'a' ? (t.stealsA||0) : (t.stealsB||0);
    if(!t.winner && !t.seriesWinner && myCharges > 0 && t.turn !== myRole){
      const stealBtn = document.createElement('button');
      stealBtn.className = 'ttt-steal-btn';
      stealBtn.textContent = `Voler ce tour (${myCharges} dispo)`;
      stealBtn.addEventListener('click', stealTurn);
      tttStealRowEl.appendChild(stealBtn);
    }

    tttToastEl.textContent = t.lastToast || '';

    const cells = tttBoardEl.querySelectorAll('.ttt-cell');
    cells.forEach((cell, i) => {
      const val = t.board[i];
      cell.textContent = val || '';
      cell.classList.toggle('filled', !!val);
      cell.classList.toggle('x', val === 'X');
      cell.classList.toggle('o', val === 'O');
      cell.classList.toggle('win', !!(t.line && t.line.includes(i)));
      const myTurn = !t.winner && !t.seriesWinner && t.turn === myRole && !val;
      cell.classList.toggle('disabled', !myTurn);
    });

    if(t.seriesWinner){
      clearSuddenTimer();
      const winnerName = t.seriesWinner === 'a' ? session.nameA : session.nameB;
      tttTurnEl.textContent = `${winnerName} remporte la série ${Math.max(t.seriesA,t.seriesB)} - ${Math.min(t.seriesA,t.seriesB)} 🏆`;
      if(!t.winnerChoiceDone){
        tttWinnerChoiceEl.style.display = 'block';
        tttWinnerChoiceLabelEl.textContent = myRole === t.seriesWinner
          ? `Bravo ! À toi de décider la suite :`
          : `${winnerName} a gagné la série et choisit la suite…`;
        tttPickThemeBtn.style.display = myRole === t.seriesWinner ? '' : 'none';
        tttPickHardBtn.style.display = myRole === t.seriesWinner ? '' : 'none';
        tttPickSoftBtn.style.display = myRole === t.seriesWinner ? '' : 'none';
      } else {
        tttWinnerChoiceEl.style.display = 'none';
      }
    } else if(t.winner === 'tie'){
      clearSuddenTimer();
      tttWinnerChoiceEl.style.display = 'none';
      tttTurnEl.textContent = "Match nul — personne ne gagne cette manche.";
    } else if(t.winner === 'X' || t.winner === 'O'){
      clearSuddenTimer();
      tttWinnerChoiceEl.style.display = 'none';
      const wname = t.winner === 'X' ? session.nameA : session.nameB;
      tttTurnEl.textContent = `${wname} gagne cette manche ! 🎉 (${(t.seriesA||0)} - ${(t.seriesB||0)})`;
    } else {
      tttWinnerChoiceEl.style.display = 'none';
      const turnName = t.turn === 'a' ? session.nameA : session.nameB;
      tttTurnEl.textContent = t.turn === myRole
        ? `À toi de jouer, ${turnName} !`
        : `Au tour de ${turnName}…`;
      startSuddenTimerIfNeeded(t);
      if(t.turn !== myRole || !t.sudden) clearSuddenTimer();
    }
  }

  (async function tryResume(){
    const l = local.get();
    if(l.code && l.role){
      try{
        const s = await loadSession(l.code);
        if(s){
          session = s;
          myRole = l.role;
          boot();
        }
      }catch(e){ /* fall back to setup screen */ }
    }
  })();
})();
</script>

</body>
</html>