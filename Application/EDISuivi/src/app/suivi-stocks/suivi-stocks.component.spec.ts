import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SuiviStocksComponent } from './suivi-stocks.component';

describe('SuiviStocksComponent', () => {
  let component: SuiviStocksComponent;
  let fixture: ComponentFixture<SuiviStocksComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SuiviStocksComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SuiviStocksComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
