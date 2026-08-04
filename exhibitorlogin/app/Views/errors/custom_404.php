<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access Restricted — VeConnect</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
  :root{
    --navy: #12172B;
    --navy-deep: #0B0E1D;
    --amber: #E8A33D;
    --amber-soft: #F3C878;
    --paper: #F7F4EC;
    --paper-dim: #E9E4D6;
    --ink: #1E2233;
    --muted: #7E8398;
  }

  *{ box-sizing: border-box; }

  body{
    margin: 0;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    background:
      radial-gradient(circle at 18% 20%, rgba(232,163,61,0.10), transparent 45%),
      radial-gradient(circle at 82% 78%, rgba(232,163,61,0.07), transparent 40%),
      var(--navy);
    color: var(--paper);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
    position: relative;
    overflow: hidden;
  }

  /* faint dotted grid texture */
  body::before{
    content: "";
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(247,244,236,0.05) 1px, transparent 1px);
    background-size: 26px 26px;
    pointer-events: none;
  }

  .wrap{
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 28px;
    animation: rise 0.7s cubic-bezier(.2,.7,.3,1) both;
  }

  .eyebrow{
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--amber-soft);
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .eyebrow::before, .eyebrow::after{
    content: "";
    width: 26px;
    height: 1px;
    background: rgba(232,163,61,0.5);
  }

  /* ===== Badge (signature element) ===== */
  .badge{
    width: min(400px, 88vw);
    background: var(--paper);
    color: var(--ink);
    border-radius: 18px;
    box-shadow:
      0 30px 60px -20px rgba(0,0,0,0.55),
      0 0 0 1px rgba(255,255,255,0.04);
    position: relative;
    padding-top: 26px;
    transform: rotate(-1.2deg);
  }

  .lanyard-hole{
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--navy-deep);
    box-shadow: inset 0 3px 6px rgba(0,0,0,0.5);
    margin: 0 auto 18px;
  }

  .badge-head{
    padding: 0 30px 20px;
    text-align: center;
    border-bottom: 1.5px dashed rgba(30,34,51,0.22);
  }

  .badge-org{
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 10px;
  }

  .stamp{
    display: inline-block;
    font-family: 'Fraunces', serif;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #B4402A;
    border: 2px solid #B4402A;
    border-radius: 6px;
    padding: 6px 16px;
    transform: rotate(-4deg);
    position: relative;
  }
  .stamp::before, .stamp::after{
    content: "";
    position: absolute;
    top: -3px; bottom: -3px;
    width: 2px;
    background: repeating-linear-gradient(#B4402A 0 4px, transparent 4px 7px);
  }
  .stamp::before{ left: -3px; }
  .stamp::after{ right: -3px; }

  .badge-body{
    padding: 26px 30px 30px;
  }

  h1{
    font-family: 'Fraunces', serif;
    font-weight: 500;
    font-size: 28px;
    line-height: 1.3;
    margin: 0 0 24px;
    letter-spacing: -0.01em;
  }

  p{
    font-size: 14.5px;
    line-height: 1.65;
    color: #4B506A;
    margin: 0 0 22px;
  }

  .id-row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: var(--muted);
    letter-spacing: 0.04em;
    padding-top: 16px;
    border-top: 1.5px dashed rgba(30,34,51,0.22);
  }

  .barcode{
    display: flex;
    gap: 2px;
    align-items: flex-end;
    height: 20px;
  }
  .barcode span{
    display: block;
    width: 2px;
    background: var(--ink);
    opacity: 0.6;
  }

  .actions{
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .btn{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    transition: transform 0.25s ease, background 0.25s ease;
  }

  .btn-primary{
    background: var(--amber);
    color: var(--navy-deep);
  }
  .btn-primary:hover{
    background: var(--amber-soft);
    transform: translateY(-2px);
  }

  .btn-ghost{
    color: var(--paper);
    border: 1px solid rgba(247,244,236,0.25);
  }
  .btn-ghost:hover{
    border-color: rgba(247,244,236,0.5);
    transform: translateY(-2px);
  }

  @keyframes rise{
    from{ opacity: 0; transform: translateY(18px); }
    to{ opacity: 1; transform: translateY(0); }
  }

  @media (prefers-reduced-motion: reduce){
    .wrap{ animation: none; }
    .btn{ transition: none; }
  }

  @media (max-width: 420px){
    h1{ font-size: 22px; }
    .badge{ transform: none; }
  }
</style>
</head>
<body>

  <div class="wrap">
    <div class="eyebrow">Restricted Entry</div>

    <div class="badge">
      <div class="lanyard-hole"></div>

      <div class="badge-head">
        <div class="badge-org">VeConnect Exhibitor Portal</div>
        <span class="stamp">Access Denied</span>
      </div>

      <div class="badge-body">
        <h1>This page cannot be&nbsp;accessed directly</h1>

        <div class="id-row">
          <span>REF · NO-DIRECT-ACCESS</span>
          <div class="barcode" aria-hidden="true">
            <span style="height:14px"></span><span style="height:20px"></span>
            <span style="height:10px"></span><span style="height:18px"></span>
            <span style="height:8px"></span><span style="height:16px"></span>
            <span style="height:20px"></span><span style="height:12px"></span>
            <span style="height:18px"></span><span style="height:10px"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>