<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login · 850 FICO Club</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: { colors: {
    olive: { DEFAULT: '#3d4020', dark: '#2a2c15' },
    gold:  { DEFAULT: '#c9a54a', dark: '#a38538' },
    paper: '#faf8f2',
  }}}};
</script>
</head>
<body class="min-h-screen bg-olive-dark flex items-center justify-center p-4">

<div class="w-full max-w-sm bg-paper rounded-xl shadow-2xl p-8">
  <div class="text-center mb-6">
    <div class="inline-flex w-14 h-14 rounded-full bg-gold items-center justify-center font-bold text-olive-dark text-lg mb-3">850</div>
    <h1 class="text-xl font-semibold text-olive-dark">FICO Club Admin</h1>
    <p class="text-sm text-olive/60 mt-1">Sign in to continue</p>
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
            class="w-full py-2.5 bg-olive-dark text-paper rounded-lg text-sm font-semibold hover:bg-olive transition">
      Sign in
    </button>
  </form>
</div>

</body>
</html>
