
def clean_utf(name):
    whitelist = set('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ')
    return ''.join(filter(self.whitelist.__contains__, name))