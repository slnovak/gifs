import glob
import json

def main():
  gifs = glob.glob('*/*.gif')
  file = open('javascript/gifs.js', 'w')
  file.write('gifs = ' + json.JSONEncoder().encode(gifs) + ';')
  file.close()

if __name__ == '__main__':
  main()
