Plugin CMS Auto Installer, plugin untuk [Ciberindo](https://git.cms-network.com:1688/hendra/ciberindo)

# Version
- 2018-07-24 => ver 0.1

# Cara Install
- Upload forder cmscontrolpanel ke AMEMBERROOT/application/default/plugins/misc
- Aktifkan plugin di admin page

# TODO
- 2018-07-24
    - ~~Buat logic nya di rubah, user sign up produk -> save database -> baru execute create secara background (dgn detail dr database)~~
    - Buat Control panel user untuk manage script nya via panel amember
    - Buat Dynamic CMSApp nya.. agar dapat ditambahkan manual via panel Admin
    - Auto update CMSAPP dari setiap repository git masing2
    - Logic Update CMSApp di setiap client bagi yang membutuhkan


# Tambahan Install
Pada .htaccess ditambahkan

        RewriteRule ^cms/(.*) cms/index.php?page=$1 [L]

sehingga pada .htaccess nya menjadi

    <IfModule mod_rewrite.c>
        RewriteEngine on
    # You may need to uncomment the following line if rewrite fails to work
    # RewriteBase must be setup to base URL of your aMember installation without
    # domain name
        RewriteBase /
        # Workaround for a bug introduced in Apache 2.4.18 (caused endless loop)
        RewriteCond %{ENV:REDIRECT_STATUS} 200
        RewriteRule .* - [L]
        # Continue to normal aMember rules
        RewriteRule ^public public.php [L]
        RewriteRule ^js.php js.php [L]
        RewriteRule ^cms/(.*) cms/index.php?page=$1 [L]
        RewriteRule !\.(js|ico|gif|jpg|png|css|swf|csv|html|pdf|woff|ttf|eot|svg)$ index.php
    </IfModule>

# ChangeLog
- 2018-07-24
	- asdf
	- asdf
	- asdf

- 2018-07-24
