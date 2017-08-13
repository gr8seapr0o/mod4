/**
 * Created by ADAM on 19.10.2016.
 */
function confirmDelete() {
    if (confirm("Delete this item?")) {
        return true;
    } else {
        return false;
    }
}