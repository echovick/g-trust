<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Home
Route::get('/', function () {
    return Inertia::render('LandingPage');
})->name('home');

// About Pages
Route::prefix('about')->name('about.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('about/Index');
    })->name('index');

    Route::get('/careers', function () {
        return Inertia::render('about/Careers');
    })->name('careers');

    Route::get('/history', function () {
        return Inertia::render('about/History');
    })->name('history');

    Route::get('/insight', function () {
        return Inertia::render('about/Insight');
    })->name('insight');

    Route::get('/investors', function () {
        return Inertia::render('about/Investors');
    })->name('investors');

    Route::get('/accessibility', function () {
        return Inertia::render('about/Accessibility');
    })->name('accessibility');
});

// Personal Banking
Route::prefix('banking/personal')->name('banking.personal.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('banking/personal/Index');
    })->name('index');

    Route::get('/checking', function () {
        return Inertia::render('banking/personal/Checking');
    })->name('checking');

    Route::get('/savings', function () {
        return Inertia::render('banking/personal/Savings');
    })->name('savings');

    Route::get('/managers', function () {
        return Inertia::render('banking/personal/Managers');
    })->name('managers');
});

// Business Banking
Route::prefix('banking/business')->name('banking.business.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('banking/business/Index');
    })->name('index');

    Route::get('/checking', function () {
        return Inertia::render('banking/business/Checking');
    })->name('checking');

    Route::get('/savings', function () {
        return Inertia::render('banking/business/Savings');
    })->name('savings');

    Route::get('/sba', function () {
        return Inertia::render('banking/business/Administration');
    })->name('sba');

    Route::get('/services', function () {
        return Inertia::render('banking/business/Services');
    })->name('services');

    Route::get('/e-banking', function () {
        return Inertia::render('banking/business/EBanking');
    })->name('ebanking');
});

// Credit Cards
Route::prefix('cards')->name('cards.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('cards/Index');
    })->name('index');

    Route::get('/find', function () {
        return Inertia::render('cards/Find');
    })->name('find');

    Route::get('/benefits', function () {
        return Inertia::render('cards/Benefits');
    })->name('benefits');

    Route::get('/apply', function () {
        return Inertia::render('cards/Apply');
    })->name('apply');
});

// Services
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('services/Index');
    })->name('index');

    Route::get('/mobile-banking', function () {
        return Inertia::render('services/MobileBanking');
    })->name('mobile');

    Route::get('/online-business', function () {
        return Inertia::render('services/OnlineBusiness');
    })->name('online-business');

    Route::get('/business-plan', function () {
        return Inertia::render('services/BusinessPlan');
    })->name('business-plan');
});

// News
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('news/Index');
    })->name('index');

    Route::get('/media', function () {
        return Inertia::render('news/Media');
    })->name('media');

    Route::get('/blog', function () {
        return Inertia::render('news/Blog');
    })->name('blog');

    Route::get('/events', function () {
        return Inertia::render('news/Events');
    })->name('events');
});

// Support
Route::get('/contact', function () {
    return Inertia::render('support/Contact');
})->name('contact');

Route::get('/locations', function () {
    return Inertia::render('support/Locations');
})->name('locations');

Route::get('/help', function () {
    return Inertia::render('support/Help');
})->name('help');

Route::get('/privacy', function () {
    return Inertia::render('support/Privacy');
})->name('privacy');

// Utility Pages
Route::get('/features', function () {
    return Inertia::render('Features');
})->name('features');

Route::get('/search', function () {
    return Inertia::render('Search');
})->name('search');

// Dashboard (protected)
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
