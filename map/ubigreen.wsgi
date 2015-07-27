activate_this = '/opt/mapproxy/bin/activate_this.py'
execfile(activate_this, dict(__file__=activate_this))
from mapproxy.multiapp import make_wsgi_app
application = make_wsgi_app('/apps/gisclient-3/map/ubigreen/', allow_listing=True)