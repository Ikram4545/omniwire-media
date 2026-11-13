<?php
/**
 * OmniWire Media - Contact Editorial Desk
 * Target File: templates/contact.php
 */
$contactSuccess = false;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['contact_desk_submit'])) {
    $cName = trim($_POST['name'] ?? '');
    $cEmail = trim($_POST['email'] ?? '');
    $cSubject = trim($_POST['subject'] ?? 'General Inquiry');
    $cMessage = trim($_POST['message'] ?? '');
    if (!empty($cName) && !empty($cEmail) && !empty($cMessage)) {
        if (isset($db) && $db instanceof PDO) {
            try {
                $stmt = $db->prepare("INSERT INTO guest_post_leads (name, email, website_url, budget, message) VALUES (?, ?, 'https://omniwiremedia.com/contact', ?, ?)");
                $stmt->execute([$cName, $cEmail, $cSubject, "[Contact Desk - {$cSubject}] " . $cMessage]);
            } catch (\Throwable $e) {}
        }
        if (function_exists('sendOutreachMimeEmail')) {
            $htmlMail = "<div style='font-family:sans-serif;padding:16px;'>"
                      . "<h3>New Editorial Contact Desk Submission ({$cSubject})</h3>"
                      . "<p><strong>From:</strong> " . htmlspecialchars($cName) . " &lt;" . htmlspecialchars($cEmail) . "&gt;</p>"
                      . "<p><strong>Department:</strong> " . htmlspecialchars($cSubject) . "</p>"
                      . "<p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($cMessage)) . "</p>"
                      . "</div>";
            @sendOutreachMimeEmail('ikramrajput714@gmail.com', 'OmniWire Editorial Desk', "[OmniWire Contact Desk] {$cSubject} - {$cName}", $htmlMail);
        }
        $contactSuccess = true;
    }
}
?>
<div class="max-w-3xl mx-auto space-y-8 animate-fade-in-up">
  <div class="border-b border-slate-800 pb-6">
    <span class="text-xs font-bold uppercase tracking-wider text-emerald-500 block mb-2">Direct Newsroom &amp; Compliance Desk</span>
    <h1 class="text-3xl sm:text-4xl font-serif font-extrabold text-white">Contact Editorial Desk</h1>
    <p class="text-xs sm:text-sm text-slate-400 mt-2">Reach out for news tips, guest posting inquiries, or syndication requests</p>
  </div>

  <?php if ($contactSuccess): ?>
  <div class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm font-semibold flex items-center justify-between">
    <span>✓ Thank you! Your inquiry has been transmitted directly to the OmniWire Editorial Desk (<!--email_off-->editorial@omniwiremedia.com<!--/email_off-->). Expected response: Within 2 Hours.</span>
  </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
      <span class="text-xs font-bold uppercase tracking-wider text-emerald-500 block mb-2">Editorial Inquiries</span>
      <h4 class="font-bold text-white mb-1">OmniWire Editorial Desk</h4>
      <p class="text-xs text-slate-400 mb-4">Chief Publisher &amp; Editorial Operations</p>
      <div class="text-xs space-y-2">
        <div><strong class="text-slate-300">Primary Contact Email:</strong> <!--email_off--><a href="mailto:editorial@omniwiremedia.com" class="text-emerald-400 font-semibold hover:opacity-80">editorial@omniwiremedia.com</a><!--/email_off--></div>
        <div><strong class="text-slate-300">Desk Publisher:</strong> <a href="<?= htmlspecialchars($footerWhatsAppLink ?? 'https://omniwiremedia.com/contact-us') ?>" class="text-slate-300 hover:text-emerald-400 font-medium">OmniWire Editorial Desk</a></div>
      </div>
    </div>

    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
      <span class="text-xs font-bold uppercase tracking-wider text-blue-500 block mb-2">Desk Operations</span>
      <h4 class="font-bold text-white mb-1">OmniWire Media Newsroom</h4>
      <p class="text-xs text-slate-400 mb-4">24/7 Multi-Language Wire Operations</p>
      <div class="text-xs space-y-2 text-slate-300">
        <div>Official Inquiries: <!--email_off--><a href="mailto:editorial@omniwiremedia.com" class="text-emerald-400 font-semibold">editorial@omniwiremedia.com</a><!--/email_off--></div>
        <div>Desk Availability: <strong class="text-white">24/7 Wire Operations</strong></div>
        <div>Verified Response Window: <strong class="text-emerald-400">Within 2 Hours</strong></div>
      </div>
    </div>
  </div>

  <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 shadow-sm">
    <h3 class="text-xl font-serif font-bold text-white mb-1">Send Direct Message to Editorial Desk</h3>
    <p class="text-xs text-slate-400 mb-6">All submissions are routed directly to our editorial and compliance desk.</p>

    <form action="<?= htmlspecialchars(($basePrefix ?? '') . '/contact') ?>" method="POST" class="space-y-4 text-xs font-medium">
      <input type="hidden" name="contact_desk_submit" value="1">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 text-slate-300">Full Name *</label>
          <input type="text" name="name" required placeholder="Enter your full name" class="w-full p-3 rounded-xl bg-slate-800 border border-slate-700 outline-none focus:border-emerald-500 text-white">
        </div>
        <div>
          <label class="block mb-1 text-slate-300">Email Address *</label>
          <input type="email" name="email" required placeholder="you@domain.com" class="w-full p-3 rounded-xl bg-slate-800 border border-slate-700 outline-none focus:border-emerald-500 text-white">
        </div>
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Subject / Department *</label>
        <select name="subject" required class="w-full p-3 rounded-xl bg-slate-800 border border-slate-700 outline-none focus:border-emerald-500 text-white">
          <option value="General Inquiry">General Inquiry</option>
          <option value="News Tip">News Tip</option>
          <option value="Guest Pitch">Guest Pitch</option>
          <option value="Legal &amp; Privacy">Legal &amp; Privacy</option>
        </select>
      </div>

      <div>
        <label class="block mb-1 text-slate-300">Message *</label>
        <textarea name="message" rows="5" required placeholder="Write your inquiry, news tip, or message..." class="w-full p-3 rounded-xl bg-slate-800 border border-slate-700 outline-none focus:border-emerald-500 text-white"></textarea>
      </div>

      <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-sm bg-emerald-600 hover:bg-emerald-500 text-white transition cursor-pointer shadow-lg shadow-emerald-600/20">
        Submit Inquiry to Editorial Desk
      </button>
    </form>
  </div>
</div>
