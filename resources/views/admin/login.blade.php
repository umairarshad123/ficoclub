<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login · 850 FICO Club</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: {
    colors: {
      olive: { DEFAULT: '#1A3056', dark: '#0F2044' },
      gold:  { DEFAULT: '#22C55E', dark: '#16A34A' },
      paper: '#ffffff',
    },
    fontFamily: { sans: ['Manrope', 'system-ui', 'sans-serif'] },
  }}};
</script>
<style>.logo-850{font-family:'Sora',sans-serif;font-weight:900;letter-spacing:-.5px;line-height:1}</style>
</head>
<body class="min-h-screen bg-olive-dark flex items-center justify-center p-4">

<div class="w-full max-w-sm bg-paper rounded-2xl shadow-2xl p-8">
  <div class="text-center mb-6">
    <div class="logo-850 text-3xl"><span style="color:#22C55E">850</span> <span style="color:#F5A623">FICO</span> <span style="color:#EF4444">CLUB</span></div>
    <div class="text-[10px] font-semibold tracking-wider uppercase text-gray-400 mt-2">Credit Is King &amp; Cash Is Power</div>
    <p class="text-sm text-gray-500 mt-5">Admin · sign in to continue</p>
  </div>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-xs font-medium text-olive-dark mb-1.5">Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required autofocus
             class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold focus:border-transparent">
    </div>

    <div>
      <label class="block text-xs font-medium text-olive-dark mb-1.5">Password</label>
      <input type="password" name="password" required
             class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold focus:border-transparent">
    </div>

    <button type="submit"
            class="w-full py-3 bg-gold text-white rounded-lg text-sm font-bold hover:bg-gold-dark transition shadow-[0_4px_14px_rgba(34,197,94,.35)]">
      Sign in
    </button>
  </form>
</div>

</body>
</html>
