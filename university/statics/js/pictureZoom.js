/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var count = 10;
var imgShow;
function setPInfo(c, obj) {
    count = c;
    imgShow = document.getElementById(obj);
    if(imgShow.style.zoom !== undefined){
         imgShow.style.zoom = count + '0%';
    }else{
        //var c = count/10;
        //imgShow.style.MozTransform= 'scale('+c+')';
    }
}
function Picture() {
    count = Counting(count);
    Resize(count);
    return false;
}
function Counting(count) {
    if (event.wheelDelta >= 120) {
        count++;
    } else if (event.wheelDelta <= -120) {
        if (count > 1) {
            count--;
        }
    }
    return count;
}
function Resize(count) {
     if(imgShow.style.zoom !== undefined){
         imgShow.style.zoom = count + '0%';
    }else{
        //var c = count/10;
        //imgShow.style.MozTransform= 'scale('+c+')';
    }
}


