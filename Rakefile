require 'rake/file_utils'
require 'rake/clean'

SOURCE_FILES = Rake::FileList.new("**/*.gif") do |fl|
  fl.exclude("~*")
  fl.exclude("_*")
  fl.exclude do |f|
    `git ls-files #{f}`.empty?
  end
end

CLEAN.include(SOURCE_FILES.ext(".jpg"))

task :default => :jpg
task :jpg => SOURCE_FILES.ext(".jpg")

rule ".jpg" => ->(f){source_for_jpg(f)} do |t|
  sh "convert -monitor #{t.source}[2] #{t.name}"
end
CLOBBER << "*.jpg"

def source_for_jpg(jpg_file)
  SOURCE_FILES.detect{|f| f.ext('') == jpg_file.ext('')}
end
