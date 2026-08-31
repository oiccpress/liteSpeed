# LiteSpeed

This plugin integrates LiteSpeed or LSCache with OJS

## Usage

* Ensure your server has LiteSpeed cache enabled
* Install and enable this plugin
* Edit htaccess file
* Done

In your htaccess file you need to add the following near the top:

```htaccess
<IfModule LiteSpeed>
CacheLookup on
RewriteRule .? - [E=Cache-Vary:ojs_id]
</IfModule>
```

## OICC Press in collaboration with Invisible Dragon

![OICC Press in Collaboration with Invisible Dragon](https://images.invisibledragonltd.com/oicc-collab.png)

This project is brought to you by [Invisible Dragon](https://invisibledragonltd.com/ojs/) in collaboration with
[OICC Press](https://oiccpress.com/)

If you've enjoyed this plugin, we'd appreciate a [thank you](https://invisibledragonltd.com/thankyou).
