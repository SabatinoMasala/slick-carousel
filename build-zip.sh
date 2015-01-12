#! /bin/bash
cd trunk && zip -r --exclude=*.git* ../slick-carousel.zip . || exit 0 && cd ..
