echo '' > ../public/dist/fields.js
echo '' > ../public/dist/fields.css
cat ../public/js/checkbox-slider.js | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.js
cat ../public/js/fileupload.js | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.js
cat ../public/js/pikaday.js | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.js
cat ../public/js/redactor.js | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.js
cat ../public/js/selectize.js | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.js
cat ../public/css/fileupload.css | sed 's/\.\.\/\.\.\/bower_components/..\/..\/..\/..\/bower_components/g' >> ../public/dist/fields.css