# Hazelchat for ProcessWire

Adds your [Hazelchat](https://hazelchat.co/?utm_source=processwire&utm_medium=module&utm_campaign=readme) AI chat agent to every page of a ProcessWire site from one pasted installation code. The agent answers visitors only from the pages you approved, shows its sources and passes on their enquiries.

- Nothing to host and no AI provider keys: Hazelchat runs the agent, you approve what it may use.
- No template editing. Superusers add or change the code in the admin.
- Works with PrivacyWire's cookie banner when you want the chat to wait for consent.
- ProcessWire 3.0.200 or newer, PHP 7.3 or newer. Needs a Hazelchat account; the Free plan covers one website and stays free.

## Install

1. In Hazelchat, add your website and copy its installation code ([create a free account](https://app.hazelchat.co/signup?utm_source=processwire&utm_medium=module&utm_campaign=readme-signup)).
2. Download the module ([Hazelchat.zip](https://hazelchat.co/downloads/processwire/Hazelchat.zip)), unzip it and copy the `Hazelchat` folder into `/site/modules/`.
3. In the admin, go to **Modules**, click **Refresh**, then **Install** next to Hazelchat.
4. Paste the installation code into **Installation code** and click **Submit**.
5. Back in Hazelchat, click **Publish and switch on**. Hazelchat finds the chat on your live page and verifies the website.

Developers can also `git clone` this repository into `/site/modules/Hazelchat`, or, with debug mode on, use **Modules → New → Add Module From Directory** with the class name `Hazelchat`.

## What it does

- The page each visitor requests gets one tag just before its closing body tag:
  `<script src="https://app.hazelchat.co/widget.js" id="hazelchat-processwire" defer data-agent="<key>"></script>`
- The installation code may be the pasted code or just its 64-character key. Only a valid key is saved; anything else, such as an API key, is refused in one sentence. The script address is fixed in the code and cannot be changed in the settings.
- The admin, AJAX requests, FormBuilder's form frame, templates whose content type is not HTML, pages rendered inside other templates and output without a closing body tag stay as they are.
- **Wait for my consent manager** (off) adds `data-require-consent="true"`: the chat starts only once your consent manager allows it. See [Visitor consent](https://hazelchat.co/integrations/processwire/#consent).
- The tag is the same for every visitor, so full-page caches such as ProCache can keep it.

### With PrivacyWire

With [PrivacyWire](https://processwire.com/modules/privacywire/) switched on and **Wait for my consent manager** on, the module prints the tag in PrivacyWire's own inert form, in its **Functional** cookie group. Nothing from Hazelchat loads until the visitor allows Functional cookies; then PrivacyWire starts the chat on the same page. A visitor who withdraws consent has no chat from the next page on.

If PrivacyWire's banner does not offer Functional cookies, the chat starts only for visitors who accept all cookies, and the module's settings say so. Add Functional cookies to PrivacyWire's cookie groups to let visitors choose it.

Without PrivacyWire, the chat's script loads with the page and then waits, storing nothing, until your consent manager allows it.

## Data and third parties

- Visitors' browsers load the chat from `app.hazelchat.co` (Hazelchat, Germany). Hazelchat processes the questions they type and, if they leave an enquiry, their name, email address and message ([privacy notice](https://hazelchat.co/privacy/), [terms](https://hazelchat.co/terms/)).
- The chat sets no cookies. It keeps a reference to the conversation in session storage until the tab is closed (details on the [cookie banner page](https://hazelchat.co/integrations/cookie-banners/)).
- The module itself makes no requests from ProcessWire, adds no tables, fields, templates or pages, and stores only the key and the switch in its module settings.

## Remove

Empty **Installation code** and click **Submit**, or uninstall the module under **Modules**. Your Hazelchat account and plan are not affected.

## Deutsch

Hazelchat fügt Ihren KI-Chat-Agenten mit einem einzigen Installationscode in jede Seite Ihrer ProcessWire-Site ein. Kopieren Sie den Ordner `Hazelchat` nach `/site/modules/`, klicken Sie unter **Modules** auf **Refresh** und dann bei Hazelchat auf **Install**, fügen Sie den Code aus Hazelchat ein und klicken Sie auf **Submit**. Die Browser Ihrer Besucher laden den Chat von `app.hazelchat.co`; deren Fragen und Anfragedaten verarbeitet Hazelchat. Sie brauchen ein Hazelchat-Konto; der Free-Tarif gilt für eine Website und bleibt kostenlos. [Anleitung auf Deutsch](https://hazelchat.co/de/integrations/processwire/)

## Licence

MIT. ProcessWire is a trademark of its makers; Hazelchat is not affiliated with or endorsed by them.
