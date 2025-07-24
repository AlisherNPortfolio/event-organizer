# Event organizer website

Xatolar bilan ishlash

1. Dockerda konteyner ko'targandan keyin view larga permission denied bersa, `docker exec -it app_backend sh` bilan php konteyner ichiga kirilib `chmod -R guo+w storage` buyrug'i bilan storage papkaga permission beriladi. Keyin `artisan cache:clear` bilan kesh tozalanadi
