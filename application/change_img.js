function change_img(img1,img2,lang) {
  if (document.images) {
    active = new Image ();
    normal = new Image ();
    active.src = '/pic/' + img2 +'_'+ lang +  '.png';
    normal.src = '/pic/' + img1 +'_'+ lang + '.png';
    if (img1 != img2) {
      document.images[img1].src = active.src;
    } else {
      document.images[img1].src = normal.src;
    }
  }
}
