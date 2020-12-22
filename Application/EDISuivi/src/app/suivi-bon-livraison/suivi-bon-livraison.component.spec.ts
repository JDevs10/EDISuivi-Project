import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SuiviBonLivraisonComponent } from './suivi-bon-livraison.component';

describe('SuiviBonLivraisonComponent', () => {
  let component: SuiviBonLivraisonComponent;
  let fixture: ComponentFixture<SuiviBonLivraisonComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SuiviBonLivraisonComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SuiviBonLivraisonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
